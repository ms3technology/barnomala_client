<?php

namespace App\Services;

class ThemeService
{
    /**
     * Resolve the component view name for a given homepage section.
     *
     * Reads the option key registered in `config/themes.php` (via the
     * setting() helper) and falls back to the configured default. The
     * result is a dot-path that pairs with Blade's <x-dynamic-component>:
     *
     *   $theme->component('slider')   // "homepage.sliders.slider-v1"
     *
     * @param  string  $section  Section key (e.g. "slider", "feature").
     * @return string  Blade view path.
     */
    public function component(string $section): string
    {
        $design  = $this->currentValue($section);
        $plural  = $this->pluralize($section);
        $name    = $this->sanitize($design);

        return "homepage.{$plural}.{$name}";
    }

    /**
     * Get the configured default design for a section.
     */
    public function defaultFor(string $section): string
    {
        return (string) config("themes.{$section}.default", "{$section}-v1");
    }

    /**
     * Get the [key => label] map of designs registered for a section.
     * Used by admin views to render the design-selector dropdown.
     *
     * @return array<string, string>
     */
    public function available(string $section): array
    {
        $type = config("themes.{$section}.type", 'design');

        return $type === 'option'
            ? (array) config("themes.{$section}.options", [])
            : (array) config("themes.{$section}.designs", []);
    }

    /**
     * Human-friendly label for a section (used in the admin UI).
     */
    public function label(string $section): string
    {
        return (string) config("themes.{$section}.label", ucfirst($section));
    }

    /**
     * Storage key for a section's value in the options table.
     */
    public function optionKey(string $section): string
    {
        return (string) config(
            "themes.{$section}.option_key",
            "institute.theme.{$section}"
        );
    }

    /**
     * Type of section: 'design' (selects a Blade component) or
     * 'option' (a simple key/value dropdown).
     */
    public function typeOf(string $section): string
    {
        return (string) config("themes.{$section}.type", 'design');
    }

    /**
     * Resolve the current stored value for a section, falling back to the
     * configured default when no value has been persisted.
     *
     * Reads only the canonical option_key for the section. Legacy keys are
     * no longer consulted at runtime — they were migrated to the canonical
     * keys by `php artisan theme:migrate-legacy`.
     */
    public function currentValue(string $section): mixed
    {
        $value = setting($this->optionKey($section));

        return ($value !== null && $value !== '' && $value !== [])
            ? $value
            : $this->defaultFor($section);
    }

    /**
     * All registered sections. Used to render every selector from a
     * single @foreach in the admin view, so adding a new section in
     * config/themes.php automatically surfaces its dropdown.
     *
     * @return array<string, array<string, mixed>>
     */
    public function sections(): array
    {
        return (array) config('themes', []);
    }

    /**
     * "slider" -> "sliders", "feature" -> "features".
     * Used to map the singular section key to the directory name.
     */
    protected function pluralize(string $section): string
    {
        return $section[strlen($section) - 1] === 's' ? $section : $section . 's';
    }

    /**
     * Strip characters that aren't safe inside a Blade component path.
     */
    protected function sanitize(string $design): string
    {
        return preg_replace('/[^a-z0-9_\-]/i', '', $design) ?? '';
    }
}
