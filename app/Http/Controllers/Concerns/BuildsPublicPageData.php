<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Option;
use App\Services\ThemeService;

trait BuildsPublicPageData
{
    protected function getPublicPageData(): array
    {
        $this->incrementVisitorCount();

        $options = Option::all()->pluck('value', 'option_key')->toArray();

        return [
            'options' => $options,
            'navigationItems' => $this->getNavigationItems(),
            'importantLinks' => $this->getImportantLinks($options),
            'theme' => app(ThemeService::class),
        ];
    }

    protected function getNavigationItems(): array
    {
        return app(ThemeService::class)->navigationItems();
    }

    protected function getImportantLinks(array $options): array
    {
        $fallback = config('navigation.important_links', []);
        $raw = $options['institute.links.important_json'] ?? null;

        if (empty($raw)) {
            return $fallback;
        }

        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }

        if (!is_array($raw)) {
            return $fallback;
        }

        $links = array_values(array_filter(array_map(function ($item) {
            $title = trim((string) ($item['title'] ?? ''));
            $url = trim((string) ($item['url'] ?? ''));

            if ($title === '' || $url === '') {
                return null;
            }

            return [
                'title' => $title,
                'url' => $url,
            ];
        }, $raw)));

        return !empty($links) ? $links : $fallback;
    }

    protected function incrementVisitorCount(): void
    {
        $sessionKey = 'has_visited_site';

        if (!session()->has($sessionKey)) {
            $option = Option::where('option_key', 'site.visitor_count')->first();

            if (!$option) {
                Option::create([
                    'option_key' => 'site.visitor_count',
                    'option_value' => '1',
                    'value_type' => 'integer',
                ]);
            } else {
                $option->option_value = (int) $option->option_value + 1;
                $option->save();
            }

            session()->put($sessionKey, true);
        }
    }
}
