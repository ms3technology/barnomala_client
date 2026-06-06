<?php

use App\Models\Option;
use Illuminate\Support\Facades\Cache;

if (! function_exists('setting')) {
    /**
     * Read a stored option by its key with an optional default.
     *
     * Values are cached per-request to avoid repeated database hits inside
     * the same response cycle (homepage, header, partials all call this).
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    function setting($key, $default = null)
    {
        if ($key === null || $key === '') {
            return $default;
        }

        $options = Cache::rememberForever('settings.all', function () {
            return Option::pluck('option_value', 'option_key')->all();
        });

        return array_key_exists($key, $options) ? $options[$key] : $default;
    }
}

if (! function_exists('setting_forget')) {
    /**
     * Flush the cached options map. Call this from Option observers /
     * controllers after writes so the next read picks up fresh values.
     */
    function setting_forget(): void
    {
        Cache::forget('settings.all');
    }
}
