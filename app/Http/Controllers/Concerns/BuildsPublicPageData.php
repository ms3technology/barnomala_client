<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Option;

trait BuildsPublicPageData
{
    protected function getPublicPageData(): array
    {
        $this->incrementVisitorCount();

        $options = Option::query()
            ->get()
            ->pluck('value', 'option_key')
            ->toArray();

        return [
            'options' => $options,
            'navigationItems' => $this->getNavigationItems(),
            'importantLinks' => config('navigation.important_links', []),
        ];
    }

    protected function getNavigationItems(): array
    {
        return array_map(function ($item) {
            if (isset($item['route'])) {
                $item['url'] = route($item['route']);
            }

            if (!empty($item['children'])) {
                $item['children'] = array_map(function ($child) {
                    if (isset($child['route'])) {
                        $child['url'] = route($child['route']);
                    }
                    return $child;
                }, $item['children']);
            }

            return $item;
        }, config('navigation.navigation_items', []));
    }

    protected function getImportantLinks(): array
    {
        return config('navigation.important_links', []);
    }

    protected function incrementVisitorCount(): void
    {
        $sessionKey = 'has_visited_site';

        if (!session()->has($sessionKey)) {
            $option = Option::where('option_key', 'site.visitor_count')->first();

            if (!$option) {
                $option = Option::create([
                    'option_key' => 'site.visitor_count',
                    'value' => 1,
                ]);
            } else {
                $option->update([
                    'value' => (int) $option->value + 1,
                ]);
            }

            session()->put($sessionKey, true);
        }
    }
}
