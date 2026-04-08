<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $options = Option::all()->groupBy(function($item) {
            $parts = explode('.', $item->option_key);
            return $parts[1] ?? 'general';
        });

        return view('admin.options.index', compact('options'));
    }

    /**
     * Display branding management.
     */
    public function branding()
    {
        $options = Option::where('option_key', 'like', 'institute.branding.%')->get()->pluck('option_value', 'option_key');
        return view('admin.options.branding', compact('options'));
    }

    /**
     * Display slider management.
     */
    public function slider()
    {
        $option = Option::where('option_key', 'institute.branding.slider_json')->first();
        $sliders = $option ? json_decode($option->option_value, true) ?: [] : [];
        
        $options = Option::whereIn('option_key', ['institute.hero.type'])->get()->pluck('option_value', 'option_key');

        return view('admin.options.slider', compact('sliders', 'options'));
    }

    /**
     * Update sliders
     */
    public function updateSlider(Request $request)
    {
        $sliders = [];
        
        // Handle existing sliders and updates
        if ($request->has('existing_sliders')) {
            foreach ($request->existing_sliders as $index => $slider) {
                if (isset($slider['delete']) && $slider['delete'] == '1') {
                    if (isset($slider['path'])) {
                        Storage::disk('public')->delete($slider['path']);
                    }
                    continue;
                }
                
                $item = [
                    'url' => $slider['url'] ?? '',
                    'path' => $slider['path'] ?? '',
                    'title' => $slider['title'] ?? '',
                    'order' => $slider['order'] ?? 0,
                ];

                if ($request->hasFile("existing_sliders.$index.image")) {
                    if (isset($slider['path'])) {
                        Storage::disk('public')->delete($slider['path']);
                    }
                    $file = $request->file("existing_sliders.$index.image");
                    $path = $file->store('sliders', 'public');
                    $item['path'] = $path;
                    $item['url'] = Storage::url($path);
                }
                
                $sliders[] = $item;
            }
        }

        // Handle new sliders
        if ($request->hasFile('new_sliders')) {
            foreach ($request->file('new_sliders') as $index => $file) {
                $path = $file->store('sliders', 'public');
                $sliders[] = [
                    'url' => Storage::url($path),
                    'path' => $path,
                    'title' => $request->new_slider_titles[$index] ?? '',
                    'order' => $request->new_slider_orders[$index] ?? 0,
                ];
            }
        }

        // Sort by order
        usort($sliders, fn($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));

        Option::updateOrCreate(
            ['option_key' => 'institute.branding.slider_json'],
            ['option_value' => json_encode($sliders), 'value_type' => 'json']
        );

        if ($request->has('hero_type')) {
            Option::updateOrCreate(
                ['option_key' => 'institute.hero.type'],
                ['option_value' => $request->hero_type, 'value_type' => 'string']
            );
        }

        return redirect()->back()->with('success', 'Sliders updated successfully.');
    }

    /**
     * Display layout management.
     */
    public function layout()
    {
        $option = Option::where('option_key', 'institute.homepage.layout')->first();
        $layout = $option ? json_decode($option->option_value, true) : [];
        return view('admin.options.layout', compact('layout'));
    }

    /**
     * Update layout settings
     */
    public function updateLayout(Request $request)
    {
        $keys = [
            'hero',
            'latest_news',
            'institute_info',
            'message_section',
            'stats_counter',
            'quick_links',
            'teachers',
            'student_demographics',
            'featured_news',
            'gallery',
        ];

        $layout = [];
        foreach ($keys as $key) {
            $layout[$key] = $request->has('layout_homepage_' . $key);
        }

        Option::updateOrCreate(
            ['option_key' => 'institute.homepage.layout'],
            ['option_value' => json_encode($layout), 'value_type' => 'json']
        );

        return redirect()->back()->with('success', 'Layout settings updated successfully.');
    }

    /**
     * Update stats and demographics.
     */
    public function updateStats(Request $request)
    {
        // Update basic stats
        if ($request->has('stats')) {
            foreach ($request->stats as $key => $value) {
                Option::updateOrCreate(
                    ['option_key' => "institute.stats.$key"],
                    ['option_value' => $value, 'value_type' => 'integer']
                );
            }
        }

        // Update demographics (JSON)
        if ($request->has('demographics')) {
            foreach ($request->demographics as $type => $data) {
                // Filter out empty keys/values
                $filteredData = [];
                if (isset($data['keys']) && isset($data['values'])) {
                    foreach ($data['keys'] as $index => $key) {
                        if (!empty($key)) {
                            $filteredData[$key] = $data['values'][$index] ?? 0;
                        }
                    }
                }
                
                Option::updateOrCreate(
                    ['option_key' => "institute.demographics.$type"],
                    ['option_value' => json_encode($filteredData), 'value_type' => 'json']
                );
            }
        }

        return redirect()->back()->with('success', 'Stats and demographics updated successfully.');
    }

    /**
     * Update branding
     */
    public function updateBranding(Request $request)
    {
        $keys = [
            'institute.branding.name',
            'institute.branding.accent_color',
            'institute.branding.banner_type',
            'institute.branding.header_bg',
        ];

        foreach ($keys as $key) {
            if ($request->has(str_replace('.', '_', $key))) {
                Option::where('option_key', $key)->update([
                    'option_value' => $request->get(str_replace('.', '_', $key))
                ]);
            }
        }

        // Handle Logo Upload
        if ($request->hasFile('logo')) {
            $oldOption = Option::where('option_key', 'institute.branding.logo_json')->first();
            if ($oldOption) {
                $oldData = json_decode($oldOption->option_value, true);
                if (isset($oldData['path'])) {
                    Storage::disk('public')->delete($oldData['path']);
                }
            }

            $path = $request->file('logo')->store('branding', 'public');
            Option::updateOrCreate(
                ['option_key' => 'institute.branding.logo_json'],
                [
                    'option_value' => json_encode(['url' => Storage::url($path), 'path' => $path]),
                    'value_type' => 'json'
                ]
            );
        }

        // Handle Banner Upload
        if ($request->hasFile('banner')) {
            $oldOption = Option::where('option_key', 'institute.branding.banner_json')->first();
            if ($oldOption) {
                $oldData = json_decode($oldOption->option_value, true);
                if (isset($oldData['path'])) {
                    Storage::disk('public')->delete($oldData['path']);
                }
            }

            $path = $request->file('banner')->store('branding', 'public');
            Option::updateOrCreate(
                ['option_key' => 'institute.branding.banner_json'],
                [
                    'option_value' => json_encode(['url' => Storage::url($path), 'path' => $path]),
                    'value_type' => 'json'
                ]
            );
        }

        return redirect()->back()->with('success', 'Branding updated successfully.');
    }

    /**
     * Display settings management (About, Identity, Contact).
     */
    public function settings()
    {
        $categories = ['about', 'identity', 'contact', 'branding'];
        $options = Option::where(function($query) use ($categories) {
            foreach ($categories as $category) {
                $query->orWhere('option_key', 'like', "institute.$category.%");
            }
        })->get()
        ->filter(function($option) {
            // Only include 'name' from branding, others stay in branding view
            if (str_contains($option->option_key, 'institute.branding.')) {
                return $option->option_key === 'institute.branding.name';
            }
            return true;
        })
        ->map(function($option) {
            // Force branding name into contact for UI grouping
            if ($option->option_key === 'institute.branding.name') {
                $option->ui_category = 'contact';
            } else {
                $parts = explode('.', $option->option_key);
                $option->ui_category = $parts[1] ?? 'general';
            }
            return $option;
        })
        ->groupBy('ui_category');

        return view('admin.options.settings', compact('options'));
    }

    /**
     * Update settings.
     */
    public function updateSettings(Request $request)
    {
        // Handle About Us Image Upload
        if ($request->hasFile('about_image')) {
            $oldOption = Option::where('option_key', 'institute.about.image_json')->first();
            if ($oldOption) {
                $oldData = json_decode($oldOption->option_value, true);
                if (isset($oldData['path'])) {
                    Storage::disk('public')->delete($oldData['path']);
                }
            }

            $path = $request->file('about_image')->store('about', 'public');
            Option::updateOrCreate(
                ['option_key' => 'institute.about.image_json'],
                [
                    'option_value' => json_encode(['url' => Storage::url($path), 'path' => $path]),
                    'value_type' => 'json'
                ]
            );
        }

        $data = $request->except(['_token', '_method', 'about_image']);

        foreach ($data as $key => $value) {
            $formattedKey = str_replace('_', '.', $key);
            
            $option = Option::where('option_key', $formattedKey)->first();
            
            if (!$option) {
                $option = Option::where('option_key', $key)->first();
            }

            if ($option) {
                $option->update(['option_value' => $value]);
                continue;
            }

            Option::create([
                'option_key' => $formattedKey,
                'option_value' => $value,
                'value_type' => 'string',
            ]);
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            $formattedKey = str_replace('_', '.', $key);
            // Some keys might have underscores that are NOT separators, but for our options key naming convention, 
            // the seeder uses dots. Our request input converts dots to underscores.
            // However, this might match multiple times if key has dots AND underscores.
            // Simplified logic: find by key and update.
            
            $option = Option::where('option_key', $formattedKey)->first();
            
            // If not found, try to find by exact key if the underscore replacement was wrong
            if (!$option) {
                $option = Option::where('option_key', $key)->first();
            }

            if ($option) {
                if ($option->value_type === 'boolean') {
                    $value = (bool) $value;
                } elseif ($option->value_type === 'integer') {
                    $value = (int) $value;
                }
                
                $option->update(['option_value' => $value]);
            }
        }

        return redirect()->route('admin.options.index')->with('success', 'Options updated successfully.');
    }
}
