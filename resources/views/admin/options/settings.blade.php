@extends('layouts.admin')

@section('title', 'Institution Settings')

@push('header_actions')
<button type="submit" form="settings-form" class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-bold rounded-xl shadow-lg text-white bg-linear-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 hover:shadow-xl hover:-translate-y-0.5">
    <i class="fas fa-save mr-2"></i>
    Save Settings
</button>
@endpush

@section('content')
<div class="space-y-8 animate-fadeInUp" x-data="{ activeTab: '{{ array_key_first($registry) }}' }">
    <!-- Main Card -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="p-6 md:p-8">
            <!-- Tabs Navigation -->
            <div class="flex space-x-1.5 bg-slate-100 dark:bg-slate-700/50 p-1.5 rounded-xl mb-8 overflow-x-auto">
                @foreach($registry as $id => $category)
                    <button @click="activeTab = '{{ $id }}'"
                            :class="activeTab === '{{ $id }}' ? 'bg-white dark:bg-slate-600 text-indigo-600 dark:text-indigo-400 shadow-sm ring-1 ring-slate-200 dark:ring-slate-500' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700'"
                            class="flex-none px-5 py-2.5 text-sm font-bold rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
                        @if(isset($category['icon']))
                            <i class="{{ $category['icon'] }}"></i>
                        @endif
                        {{ $category['label'] }}
                    </button>
                @endforeach
            </div>

            <form id="settings-form" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf

                <div class="space-y-12">
                    @foreach($registry as $id => $category)
                        <div x-show="activeTab === '{{ $id }}'" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform translate-y-4"
                             x-transition:enter-end="opacity-100 transform translate-y-0">

                            <div class="space-y-4">
                                <div class="space-y-4 group bg-slate-50/50 dark:bg-slate-700/20 border border-slate-100 dark:border-slate-600 rounded-xl p-5 hover:bg-white dark:hover:bg-slate-700/40 hover:border-indigo-200 dark:hover:border-indigo-700 hover:shadow-md transition-all duration-200">
                                    @foreach($category['options'] as $key => $meta)
                                        @if ($key !== 'institute.branding.logo_json')
                                        <div class="flex flex-col md:flex-row md:items-start gap-4">
                                            <label for="{{ $key }}" class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide md:w-1/4 shrink-0 pt-1">
                                                {{ $meta['label'] }}
                                            </label>

                                            <div class="flex-1 md:w-3/4">
                                                @if($meta['type'] === 'image')
                                                    <div class="space-y-4" x-data="{ photoName: null, photoPreview: null }">
                                                        <div class="relative rounded-xl overflow-hidden border border-slate-200 dark:border-slate-600" x-show="! photoPreview">
                                                            @php
                                                                $option = $existingOptions->get($key);
                                                                $imageUrl = $option ? (json_decode($option->option_value, true)['url'] ?? asset('images/placeholder.png')) : asset('images/placeholder.png');
                                                            @endphp
                                                            <img src="{{ $imageUrl }}" class="h-44 w-full object-cover">
                                                        </div>

                                                        <div class="relative rounded-xl overflow-hidden border border-slate-200 dark:border-slate-600" x-show="photoPreview" style="display: none;">
                                                            <span class="block h-44 w-full bg-cover bg-no-repeat bg-center"
                                                                  x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                                                            </span>
                                                        </div>

                                                        <input type="file" class="hidden"
                                                               name="settings_image_{{ str_replace('.', '_', $key) }}"
                                                               id="{{ $key }}"
                                                               x-ref="photo"
                                                               x-on:change="
                                                                    photoName = $refs.photo.files[0].name;
                                                                    const reader = new FileReader();
                                                                    reader.onload = (e) => {
                                                                        photoPreview = e.target.result;
                                                                    };
                                                                    reader.readAsDataURL($refs.photo.files[0]);
                                                               ">

                                                        <button type="button" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl font-bold text-xs text-slate-700 dark:text-slate-300 uppercase tracking-widest shadow-sm hover:bg-slate-50 dark:hover:bg-slate-600 hover:border-indigo-300 dark:hover:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200" 
                                                                x-on:click.prevent="$refs.photo.click()">
                                                            <i class="fas fa-cloud-upload-alt text-indigo-500"></i>
                                                            Select New Image
                                                        </button>
                                                    </div>
                                                @elseif($meta['type'] === 'boolean')
                                                    @php
                                                        $currentBoolean = $existingOptions->get($key)->option_value ?? ($meta['default'] ?? '1');
                                                    @endphp
                                                    <label class="relative inline-flex items-center cursor-pointer">
                                                        <input type="hidden" name="settings[{{ $key }}]" value="0">
                                                        <input type="checkbox" name="settings[{{ $key }}]" value="1" class="sr-only peer" {{ $currentBoolean == '1' ? 'checked' : '' }}>
                                                        <div class="w-11 h-6 bg-slate-300 dark:bg-slate-600 rounded-full peer peer-checked:bg-linear-to-r peer-checked:from-indigo-500 peer-checked:to-purple-500 after:content-[''] after:absolute after:top-0.5 after:inset-s-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                                                        <span class="ml-3 text-sm font-medium text-slate-700 dark:text-slate-300">
                                                            {{ $currentBoolean == '1' ? 'Enabled' : 'Disabled' }}
                                                        </span>
                                                    </label>
                                                @elseif($meta['type'] === 'select')
                                                    <select name="settings[{{ $key }}]" id="{{ $key }}" class="block w-full max-w-md text-sm rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-4 py-2.5 text-slate-800 shadow-sm transition-all duration-200 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-900 hover:border-slate-400">
                                                        @foreach($meta['options'] as $value => $label)
                                                            <option value="{{ $value }}" {{ ($existingOptions->get($key)->option_value ?? $meta['default'] ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                @elseif($meta['type'] === 'textarea')
                                                    <textarea name="settings[{{ $key }}]" 
                                                            id="{{ $key }}" 
                                                            rows="3" 
                                                            placeholder="{{ $meta['placeholder'] ?? '' }}"
                                                            class="block w-full text-sm rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white dark:placeholder-slate-400 px-4 py-2.5 text-slate-800 placeholder-slate-400 shadow-sm transition-all duration-200 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-900 hover:border-slate-400">{{ $existingOptions->get($key)->option_value ?? '' }}</textarea>
                                                @elseif($meta['type'] === 'json')
                                                    @if($key === 'institute.links.important_json')
                                                        @php
                                                            $jsonRaw = old('settings.' . $key, $existingOptions->get($key)->option_value ?? '[]');
                                                            $jsonDecoded = is_string($jsonRaw) ? json_decode($jsonRaw, true) : [];
                                                            $linksForUi = is_array($jsonDecoded) ? array_values(array_filter(array_map(function ($item) {
                                                                if (!is_array($item)) {
                                                                    return null;
                                                                }
                                                                return [
                                                                    'title' => (string) ($item['title'] ?? ''),
                                                                    'url' => (string) ($item['url'] ?? ''),
                                                                ];
                                                            }, $jsonDecoded))) : [];

                                                            if (empty($linksForUi)) {
                                                                $linksForUi = [['title' => '', 'url' => '']];
                                                            }
                                                        @endphp

                                                        <div x-data="{ links: {{ \Illuminate\Support\Js::from($linksForUi) }} }" class="space-y-3">
                                                            <input type="hidden" name="settings[{{ $key }}]"
                                                                :value="JSON.stringify(links.filter(link => (link.title || '').trim() !== '' || (link.url || '').trim() !== ''))">

                                                            <template x-for="(link, index) in links" :key="index">
                                                                <div class="flex gap-2 items-center">
                                                                    <input type="text" x-model="link.title" placeholder="Link title"
                                                                        class="flex-1 block w-full text-sm rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2.5 placeholder-slate-400 shadow-sm transition-all duration-200 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                                                                    <input type="url" x-model="link.url" placeholder="https://example.com"
                                                                        class="flex-2 block w-full text-sm rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2.5 placeholder-slate-400 shadow-sm transition-all duration-200 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                                                                    <button type="button"
                                                                        @click="links.splice(index, 1); if (links.length === 0) links.push({ title: '', url: '' })"
                                                                        class="inline-flex items-center justify-center rounded-xl border border-rose-300 dark:border-rose-700 bg-rose-50 dark:bg-rose-900/30 px-3 py-2.5 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/50 transition shrink-0"
                                                                        title="Remove Link">
                                                                        <i class="fas fa-trash text-xs"></i>
                                                                    </button>
                                                                </div>
                                                            </template>

                                                            <button type="button"
                                                                @click="links.push({ title: '', url: '' })"
                                                                class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold uppercase tracking-wide rounded-xl border border-indigo-300 dark:border-indigo-700 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-all duration-200">
                                                                <i class="fas fa-plus"></i>
                                                                Add Link
                                                            </button>
                                                        </div>

                                                        @error($key)
                                                            <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                                        @enderror
                                                    @else
                                                        @php
                                                            $jsonRaw = $existingOptions->get($key)->option_value ?? '';
                                                            $jsonPretty = '';
                                                            if (!empty($jsonRaw)) {
                                                                $jsonDecoded = json_decode($jsonRaw, true);
                                                                $jsonPretty = is_array($jsonDecoded)
                                                                    ? json_encode($jsonDecoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                                                                    : $jsonRaw;
                                                            }
                                                        @endphp
                                                        <textarea name="settings[{{ $key }}]"
                                                                id="{{ $key }}"
                                                                rows="8"
                                                                placeholder="{{ $meta['placeholder'] ?? '' }}"
                                                                class="block w-full font-mono text-sm rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white dark:placeholder-slate-400 px-4 py-2.5 text-slate-800 placeholder-slate-400 shadow-sm transition-all duration-200 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-900 hover:border-slate-400">{{ old('settings.' . $key, $jsonPretty) }}</textarea>
                                                        @error($key)
                                                            <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                                        @enderror
                                                    @endif
                                                @elseif($meta['type'] === 'number')
                                                    <input type="number" 
                                                        name="settings[{{ $key }}]" 
                                                        id="{{ $key }}" 
                                                        value="{{ $existingOptions->get($key)->option_value ?? '' }}"
                                                        placeholder="{{ $meta['placeholder'] ?? '' }}"
                                                        class="block w-full max-w-md text-sm rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white dark:placeholder-slate-400 px-4 py-2.5 text-slate-800 placeholder-slate-400 shadow-sm transition-all duration-200 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-900 hover:border-slate-400">
                                                @else
                                                    <input type="{{ $meta['type'] }}" 
                                                        name="settings[{{ $key }}]" 
                                                        id="{{ $key }}" 
                                                        value="{{ $existingOptions->get($key)->option_value ?? '' }}"
                                                        placeholder="{{ $meta['placeholder'] ?? '' }}"
                                                        class="block w-full max-w-md text-sm rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white dark:placeholder-slate-400 px-4 py-2.5 text-slate-800 placeholder-slate-400 shadow-sm transition-all duration-200 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-900 hover:border-slate-400">
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection