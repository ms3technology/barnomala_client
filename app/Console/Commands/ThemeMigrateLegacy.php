<?php

namespace App\Console\Commands;

use App\Models\Option;
use App\Services\ThemeService;
use Illuminate\Console\Command;

/**
 * One-shot migration for the homepage theme system.
 *
 * Before: option values for slider type / slider design / banner type
 *         were stored under ad-hoc keys (`institute.hero.type`,
 *         `institute.hero.slider_design`, `institute.branding.banner_type`).
 *
 * After:  those values are stored under the canonical keys declared in
 *         `config/themes.php` (`institute.theme.slider_type`, etc.).
 *
 * This command reads the legacy keys listed under each section's
 * `legacy_keys` array in `config/themes.php`, copies the value to the
 * section's new `option_key`, and deletes the legacy row. It is
 * idempotent: re-running it on a clean database is a no-op.
 *
 * Once the migration has been run in every environment, the
 * `legacy_keys` entries can be removed from `config/themes.php` and
 * the runtime system in `ThemeService` will only consult the canonical
 * keys.
 *
 * Usage:
 *   php artisan theme:migrate-legacy
 *   php artisan theme:migrate-legacy --dry-run
 */
class ThemeMigrateLegacy extends Command
{
    protected $signature = 'theme:migrate-legacy
        {--dry-run : Show what would change without writing to the database}';

    protected $description = 'Migrate legacy option keys to the canonical institute.theme.* keys.';

    /**
     * Sections we ever supported legacy keys for. Kept as a static
     * allowlist so the command stays predictable even if someone adds
     * a new section without thinking about migration.
     */
    protected const MIGRATABLE_SECTIONS = ['slider', 'slider_type', 'banner_type'];

    public function handle(ThemeService $theme): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN — no changes will be written.');
        }

        $rows = [];

        foreach (self::MIGRATABLE_SECTIONS as $section) {
            $legacyKeys = (array) config("themes.{$section}.legacy_keys", []);
            $newKey     = $theme->optionKey($section);
            $default    = $theme->defaultFor($section);

            if (empty($legacyKeys)) {
                $rows[] = [$section, '(none configured)', $newKey, '—', 'skipped (no legacy_keys)'];
            }

            foreach ($legacyKeys as $legacyKey) {
                $legacy = Option::where('option_key', $legacyKey)->first();

                if (!$legacy) {
                    $rows[] = [$section, $legacyKey, $newKey, '—', 'skipped (no row)'];
                    continue;
                }

                // If the new key already has a value, the legacy row is
                // stale; just drop it.
                $existing = setting($newKey);
                if ($existing !== null && $existing !== '' && $existing !== []) {
                    $rows[] = [
                        $section,
                        $legacyKey,
                        $newKey,
                        (string) $legacy->option_value,
                        "kept existing (={$existing}); legacy will be deleted",
                    ];

                    if (!$dryRun) {
                        $legacy->delete();
                    }
                    continue;
                }

                $rows[] = [
                    $section,
                    $legacyKey,
                    $newKey,
                    (string) $legacy->option_value,
                    $dryRun ? 'would copy' : 'copied',
                ];

                if (!$dryRun) {
                    Option::updateOrCreate(
                        ['option_key' => $newKey],
                        ['option_value' => (string) $legacy->option_value, 'value_type' => 'string']
                    );
                    $legacy->delete();
                }
            }

            // Final fallback: if a section still has no value under the
            // new key, write the configured default. Keeps the home page
            // from rendering an undefined state.
            // Check the DB directly (not the cached setting() helper)
            // because we may have just written a value in this loop.
            $hasNewValue = Option::where('option_key', $newKey)->exists();

            if (!$hasNewValue) {
                $rows[] = [
                    $section,
                    '(default)',
                    $newKey,
                    $default,
                    $dryRun ? 'would write default' : 'wrote default',
                ];

                if (!$dryRun) {
                    Option::updateOrCreate(
                        ['option_key' => $newKey],
                        ['option_value' => (string) $default, 'value_type' => 'string']
                    );
                }
            }
        }

        $this->table(['Section', 'Legacy key', 'New key', 'Value', 'Action'], $rows);

        if (!$dryRun) {
            // Flush the cached options map so subsequent reads see the
            // newly written values.
            if (function_exists('setting_forget')) {
                setting_forget();
            }
            $this->info('Migration complete. Cached options flushed.');
        } else {
            $this->info('Dry run complete. Re-run without --dry-run to apply.');
        }

        return self::SUCCESS;
    }
}
