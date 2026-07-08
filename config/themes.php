<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Homepage Theme Designs & Options
    |--------------------------------------------------------------------------
    |
    | This file is the single source of truth for:
    |
    |   - Which Blade component renders each dynamic homepage section
    |     (e.g. slider-v1, slider-v2). For these sections, a `default` and a
    |     `designs` map are required.
    |
    |   - Which key/value options are exposed on the branding page
    |     (e.g. slider_type, banner_type). For these sections, only an
    |     `options` map is required.
    |
    |   - The storage `option_key` for each section (so the option layer
    |     can have any structure you like).
    |
    | To add a new section:
    |   1. Add an entry below.
    |   2. For a "design" section, create the matching Blade component in
    |      resources/views/components/homepage/{plural}/{key}.blade.php
    |   3. That's it — the Branding page renders the dropdown automatically.
    |
    | To add a new design variant:
    |   1. Create the Blade component file.
    |   2. Append its key to the `designs` array for that section.
    |
    */

    'slider_type' => [
        'label'      => 'Slider Type',
        'option_key' => 'institute.theme.slider_type',
        'type'       => 'option',
        'default'    => 'slider_with_notice',
        'options'    => [
            'slider_with_notice' => 'Slider with Notice Panel',
            'slider_only'        => 'Slider Only (Full Width)',
        ],
    ],

    'banner_type' => [
        'label'      => 'Header Banner Type',
        'option_key' => 'institute.theme.banner_type',
        'type'       => 'option',
        'default'    => 'banner_with_overlay',
        'options'    => [
            'banner_only'         => 'Banner Only (full image)',
            'info_only'           => 'Info Only (logo + identity)',
            'banner_with_overlay' => 'Banner with Info Overlay',
        ],
    ],
];
