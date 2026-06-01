<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Registries\OptionRegistry;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OptionController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

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
        $sliders = $option ? (json_decode($option->option_value, true) ?: []) : [];
        
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
                    $path = $this->imageService->convertToWebp($file, 'sliders');
                    $item['path'] = $path;
                    $item['url'] = Storage::url($path);
                }
                
                $sliders[] = $item;
            }
        }

        // Handle new sliders
        if ($request->hasFile('new_sliders')) {
            foreach ($request->file('new_sliders') as $index => $file) {
                $path = $this->imageService->convertToWebp($file, 'sliders');
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

        if ($request->has('settings')) {
            foreach ($request->get('settings') as $key => $value) {
                Option::where('option_key', $key)->update(['option_value' => $value]);
            }
        }

        return redirect()->back()->with('success', 'Sliders updated successfully.');
    }

    /**
     * Display demographics management.
     */
    public function demographics()
    {
        $keys = [
            'institute.demographics.classes',
            'institute.demographics.gender',
            'institute.demographics.religion',
            'institute.stats.classes_count',
            'institute.stats.students_count',
            'institute.stats.teachers_count',
            'institute.stats.staffs_count',
        ];

        $options = Option::whereIn('option_key', $keys)->get()->pluck('value', 'option_key');
        
        // Ensure keys exist in array even if not in DB
        foreach ($keys as $key) {
            if (!$options->has($key)) {
                $options[$key] = str_contains($key, '.stats.') ? 0 : [];
            }
        }

        $locksOption = Option::where('option_key', 'transfer.locks.json')->first();
        $locks = $locksOption ? json_decode($locksOption->option_value, true) : [];
        $isLocked = $locks['demographics'] ?? false;

        return view('admin.options.demographics', compact('options', 'isLocked'));
    }

    /**
     * Update demographics.
     */
    public function updateDemographics(Request $request)
    {
        // Handle stats
        $statsMap = [
            'stats_classes' => 'institute.stats.classes_count',
            'stats_students' => 'institute.stats.students_count',
            'stats_teachers' => 'institute.stats.teachers_count',
            'stats_staffs' => 'institute.stats.staffs_count',
        ];

        foreach ($statsMap as $inputKey => $optionKey) {
            Option::updateOrCreate(
                ['option_key' => $optionKey],
                ['option_value' => (string) $request->input($inputKey, 0), 'value_type' => 'integer']
            );
        }

        $mapToStore = [
            'classes' => 'institute.demographics.classes',
            'gender' => 'institute.demographics.gender',
            'religion' => 'institute.demographics.religion',
        ];

        foreach ($mapToStore as $inputKey => $optionKey) {
            $items = $request->input($inputKey, []);
            $jsonValue = [];
            
            if (is_array($items)) {
                foreach ($items as $item) {
                    if (!empty($item['label'])) {
                        $jsonValue[$item['label']] = (int) ($item['value'] ?? 0);
                    }
                }
            }

            Option::updateOrCreate(
                ['option_key' => $optionKey],
                ['option_value' => json_encode($jsonValue), 'value_type' => 'json']
            );
        }

        return redirect()->route('admin.demographics.index')->with('success', 'Demographics updated successfully.');
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
     * Display settings based on registry.
     */
    public function settings()
    {
        $registry = OptionRegistry::getRegistration();
        $optionKeys = OptionRegistry::getAllKeys();

        $existingOptions = Option::whereIn('option_key', $optionKeys)->get()->keyBy('option_key');

        return view('admin.options.settings', compact('registry', 'existingOptions'));
    }

    /**
     * Update settings.
     */
    public function updateSettings(Request $request)
    {
        $registry = OptionRegistry::getRegistration();
        $optionKeys = OptionRegistry::getAllKeys();
        $optionMeta = [];
        foreach ($registry as $category) {
            foreach ($category['options'] as $key => $meta) {
                $optionMeta[$key] = $meta;
            }
        }

        // Handle Image Uploads from Registry
        foreach ($registry as $category) {
            foreach ($category['options'] as $key => $meta) {
                $inputName = "settings_image_" . str_replace('.', '_', $key);
                if ($meta['type'] === 'image' && $request->hasFile($inputName)) {
                    $oldOption = Option::where('option_key', $key)->first();
                    if ($oldOption) {
                        $oldData = json_decode($oldOption->option_value, true);
                        if (is_array($oldData) && isset($oldData['path'])) {
                            Storage::disk('public')->delete($oldData['path']);
                        }
                    }

                    $path = $this->imageService->convertToWebp($request->file($inputName), 'settings');
                    Option::updateOrCreate(
                        ['option_key' => $key],
                        [
                            'option_value' => json_encode(['url' => Storage::url($path), 'path' => $path]),
                            'value_type' => 'json'
                        ]
                    );
                }
            }
        }

        // Handle regular settings
        if ($request->has('settings')) {
            foreach ($request->get('settings') as $key => $value) {
                if (in_array($key, $optionKeys)) {
                    $type = $optionMeta[$key]['type'] ?? 'text';

                    if ($type === 'json') {
                        $decoded = is_string($value) ? json_decode($value, true) : null;

                        if (!is_array($decoded)) {
                            return redirect()->back()
                                ->withInput()
                                ->withErrors([$key => 'Invalid JSON format for important links.']);
                        }

                        Option::updateOrCreate(
                            ['option_key' => $key],
                            [
                                'option_value' => json_encode($decoded),
                                'value_type' => 'json',
                            ]
                        );

                        continue;
                    }

                    Option::updateOrCreate(
                        ['option_key' => $key],
                        ['option_value' => $value]
                    );
                }
            }
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    /**
     * Update branding
     */
    public function updateBranding(Request $request)
    {
        $settings = $request->get('settings', []);

        foreach ($settings as $key => $value) {
            Option::where('option_key', $key)->update(['option_value' => $value]);
        }

        // Handle Logo Upload
        if ($request->hasFile('logo')) {
            $oldOption = Option::where('option_key', 'institute.branding.logo_json')->first();
            if ($oldOption) {
                $oldData = json_decode($oldOption->option_value, true);
                if (is_array($oldData) && isset($oldData['path'])) {
                    Storage::disk('public')->delete($oldData['path']);
                }
            }

            $path = $this->imageService->convertToWebp($request->file('logo'), 'branding');
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
                if (is_array($oldData) && isset($oldData['path'])) {
                    Storage::disk('public')->delete($oldData['path']);
                }
            }

            $path = $this->imageService->convertToWebp($request->file('banner'), 'branding');
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
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $settings = $request->get('options', []);

        foreach ($settings as $key => $value) {
            $option = Option::where('option_key', $key)->first();

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
