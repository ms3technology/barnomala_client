@extends('layouts.admin')

@section('title', 'Branding Settings')

@push('header_actions')
    <button type="submit" form="branding-form"
        class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-bold rounded-xl shadow-lg text-white bg-linear-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 hover:shadow-xl hover:-translate-y-0.5">
        <i class="fas fa-save mr-2"></i>
        Save Branding
    </button>
@endpush

@section('content')
<div class="space-y-8 animate-fadeInUp">
    <form id="branding-form" action="{{ route('admin.branding.update') }}" method="POST"
        enctype="multipart/form-data" class="space-y-8">
        @csrf

        {{-- Branding & Visuals card --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-100 dark:border-slate-700 overflow-hidden">
            <div class="p-6 md:p-8">
                <!-- Banner Section -->
                @php
                    $logo = json_decode($options['institute.branding.logo_json'] ?? '{}', true);
                    $bannerData = json_decode($options['institute.branding.banner_json'] ?? '{}', true);
                @endphp
                <div x-data="{
                    bannerPreview: '{{ $bannerData['url'] ?? '' }}',
                    handleBannerChange(e) {
                        const file = e.target.files[0];
                        if (file) {
                            this.bannerPreview = URL.createObjectURL(file);
                        }
                    }
                }">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                        <!-- Logo (3 cols) -->
                        <div class="md:col-span-3 space-y-3">
                            <label class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Logo</label>
                            <div class="relative group aspect-4/3 bg-linear-to-br from-slate-50 to-slate-100 dark:from-slate-700 dark:to-slate-600 border-2 border-dashed border-slate-200 dark:border-slate-600 rounded-2xl overflow-hidden flex items-center justify-center transition-all duration-300 hover:border-indigo-300 dark:hover:border-indigo-500 hover:shadow-lg"
                                x-data="{
                                    logoPreview: '{{ isset($logo['url']) ? $logo['url'] : '' }}',
                                    handleLogoChange(e) {
                                        const file = e.target.files[0];
                                        if (file) this.logoPreview = URL.createObjectURL(file);
                                    }
                                }">
                                <template x-if="logoPreview">
                                    <img :src="logoPreview" class="max-w-full max-h-full object-contain p-2">
                                </template>
                                <template x-if="!logoPreview">
                                    <div class="text-center p-2">
                                        <i class="fas fa-image text-slate-300 dark:text-slate-500 text-xl mb-1"></i>
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase">No Logo</p>
                                    </div>
                                </template>
                                <label class="absolute inset-0 bg-linear-to-t from-indigo-900/80 to-purple-900/60 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center cursor-pointer backdrop-blur-sm">
                                    <div class="text-center transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                        <i class="fas fa-cloud-upload-alt text-xl text-white mb-1"></i>
                                        <span class="block text-white text-[10px] font-black uppercase tracking-widest">Change Logo</span>
                                    </div>
                                    <input type="file" name="logo" class="hidden" accept="image/*"
                                        @change="handleLogoChange">
                                </label>
                            </div>
                        </div>

                        <!-- Banner (9 cols) -->
                        <div class="md:col-span-9 space-y-3">
                            <label class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Main Banner</label>
                            <div class="relative group min-h-44 w-full bg-linear-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-600 border-2 border-dashed border-slate-200 dark:border-slate-600 rounded-2xl overflow-hidden flex items-center justify-center transition-all duration-300 hover:border-indigo-300 dark:hover:border-indigo-500 hover:shadow-lg">
                                <template x-if="bannerPreview">
                                    <img :src="bannerPreview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!bannerPreview">
                                    <div class="text-center p-4">
                                        <div class="w-12 h-12 rounded-xl bg-slate-200 dark:bg-slate-600 flex items-center justify-center mx-auto mb-2">
                                            <i class="fas fa-mountain text-slate-400 dark:text-slate-500 text-lg"></i>
                                        </div>
                                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">No Banner Set</p>
                                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Upload a banner image for your site header</p>
                                    </div>
                                </template>
                                <label class="absolute inset-0 bg-linear-to-t from-indigo-900/80 to-purple-900/60 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center cursor-pointer backdrop-blur-sm">
                                    <div class="text-center transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-white mb-2"></i>
                                        <p class="text-white text-sm font-bold">Update Banner Image</p>
                                        <p class="text-white/70 text-[10px] mt-1">Click to upload</p>
                                    </div>
                                    <input type="file" name="banner" class="hidden" accept="image/*"
                                        @change="handleBannerChange">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Identity Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-16">
                    <div>
                        <!-- Top Header Toggle -->
                        <div class="md:col-span-2 bg-slate-50/50 dark:bg-slate-700/30 rounded-xl p-5 border border-slate-100 dark:border-slate-600">
                            @php
                                $showTopHeader = ($options['institute.branding.show_top_header'] ?? '1') === '1';
                            @endphp
                            <label for="institute.branding.show_top_header"
                                class="flex items-center justify-between gap-4 cursor-pointer">
                                <div class="flex items-start gap-4 min-w-0">
                                    <span class="shrink-0 w-12 h-12 rounded-xl bg-linear-to-br from-indigo-100 to-purple-100 dark:from-indigo-500/20 dark:to-purple-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                        <i class="fas fa-window-maximize text-lg"></i>
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-800 dark:text-slate-200">Top Header Bar</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Show the dark contact strip (phone, email, social links, online apply)</p>
                                    </div>
                                </div>
                                <span class="relative inline-flex items-center shrink-0">
                                    <input type="hidden" name="settings[institute.branding.show_top_header]" value="0">
                                    <input type="checkbox" id="institute.branding.show_top_header"
                                        name="settings[institute.branding.show_top_header]" value="1"
                                        class="peer sr-only"
                                        {{ $showTopHeader ? 'checked' : '' }}>
                                    <span class="w-12 h-6 bg-slate-300 dark:bg-slate-600 rounded-full peer-checked:bg-linear-to-r peer-checked:from-indigo-500 peer-checked:to-purple-500 transition-all duration-300"></span>
                                    <span class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-md transform peer-checked:translate-x-6 peer-checked:shadow-lg transition-all duration-300"></span>
                                </span>
                            </label>
                        </div>

                        @if(empty($themeSections))
                        <div class="text-center py-12 bg-slate-50/50 dark:bg-slate-700/30 rounded-xl border border-dashed border-slate-200 dark:border-slate-600">
                            <div class="w-14 h-14 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-info-circle text-slate-400 text-xl"></i>
                            </div>
                            <p class="text-sm text-slate-500 dark:text-slate-400 italic">No theme sections registered.</p>
                        </div>
                        @else
                            <div class="flex flex-col gap-6">
                                <div class="bg-slate-50/50 dark:bg-slate-700/30 rounded-xl p-4 border border-slate-100 dark:border-slate-600 hover:border-indigo-200 dark:hover:border-indigo-700 transition-all duration-200">
                                    @foreach($themeSections as $sectionKey => $section)
                                        @php
                                            $optionKey    = $theme->optionKey($sectionKey);
                                            $available    = $theme->available($sectionKey);
                                            $currentValue = $theme->currentValue($sectionKey);
                                            if (!array_key_exists((string) $currentValue, $available)) {
                                                $currentValue = $theme->defaultFor($sectionKey);
                                            }
                                            $isDesign     = $theme->typeOf($sectionKey) === 'design';
                                            $labelSuffix  = $isDesign ? ' Design' : '';
                                        @endphp
                                            <div class="mb-6 last:mb-0">
                                                <label for="{{ $optionKey }}" class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2.5">
                                                    {{ $section['label'] ?? ucfirst($sectionKey) }}{{ $labelSuffix }}
                                                </label>
                                                <select id="{{ $optionKey }}"
                                                    name="settings[{{ $optionKey }}]"
                                                    class="w-full px-3 py-2.5 border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                                                    @foreach($available as $valueKey => $valueLabel)
                                                        <option value="{{ $valueKey }}" {{ (string) $currentValue === (string) $valueKey ? 'selected' : '' }}>
                                                            {{ $valueLabel }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif                                                         

                        <!-- Side Panel Type -->
                        <div class="bg-slate-50/50 dark:bg-slate-700/30 rounded-xl p-5 border border-slate-100 dark:border-slate-600">
                            <label class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <i class="fas fa-layout text-indigo-500"></i>
                                About Side Panel Type
                            </label>
                            <select id="institute.about.side_panel_type"
                                name="settings[institute.about.side_panel_type]"
                                class="w-full px-3 py-2.5 border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                                <option value="image" {{ ($options['institute.about.side_panel_type'] ?? 'image') === 'image' ? 'selected' : '' }}>Image</option>
                                <option value="notice" {{ ($options['institute.about.side_panel_type'] ?? 'image') === 'notice' ? 'selected' : '' }}>Notice</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Header Background Color -->
                        <div class="bg-slate-50/50 dark:bg-slate-700/30 rounded-xl p-5 border border-slate-100 dark:border-slate-600">
                            <label class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <i class="fas fa-fill-drip text-indigo-500"></i>
                                Header Background
                            </label>
                            <div class="flex items-center gap-4 p-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl">
                                <input type="color" name="settings[institute.branding.header_bg]" value="{{ $options['institute.branding.header_bg'] ?? '#ffffff' }}"
                                        class="h-10 w-16 rounded-lg cursor-pointer border-none bg-transparent">
                                <span class="text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase">{{ $options['institute.branding.header_bg'] ?? '#ffffff' }}</span>
                                <span class="ml-auto text-[10px] text-slate-400 font-medium">Click to change</span>
                            </div>
                        </div>

                        <!-- Accent Color -->
                        <div class="bg-slate-50/50 dark:bg-slate-700/30 rounded-xl p-5 border border-slate-100 dark:border-slate-600">
                            <label class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <i class="fas fa-paint-brush text-indigo-500"></i>
                                Accent Color
                            </label>
                            <div x-data="{
                                selectedColor: '{{ $options['institute.branding.accent_color'] ?? '#4F46E5' }}',
                                colors: ['#4F46E5', '#2563EB', '#059669', '#DC2626', '#7C3AED', '#D97706', '#0891B2', '#4B5563']
                            }">
                                <div class="flex flex-wrap gap-3 mb-4">
                                    <template x-for="color in colors" :key="color">
                                        <button type="button" @click="selectedColor = color"
                                            :style="{ backgroundColor: color }"
                                            class="w-9 h-9 rounded-full border-2 transition-all duration-200 hover:scale-110 hover:shadow-lg"
                                            :class="selectedColor === color ? 'border-slate-900 dark:border-white scale-110 ring-2 ring-offset-2 ring-indigo-500' : 'border-transparent'">
                                        </button>
                                    </template>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <input type="color" x-model="selectedColor"
                                            class="w-10 h-10 rounded-lg border border-slate-300 dark:border-slate-600 p-0.5 cursor-pointer">
                                    </div>
                                    <div class="relative flex-1">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <span class="text-xs text-slate-400">#</span>
                                        </div>
                                        <input type="text" name="settings[institute.branding.accent_color]"
                                            x-model="selectedColor"
                                            class="w-full pl-6 pr-3 py-2.5 border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg text-sm font-mono uppercase focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- About Image -->
                        <div class="bg-slate-50/50 dark:bg-slate-700/30 rounded-xl p-5 border border-slate-100 dark:border-slate-600">
                            @php
                                $aboutImage = json_decode($options['institute.about.image_json'] ?? '{}', true);
                            @endphp
                            <label class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <i class="fas fa-image text-indigo-500"></i>
                                About Image
                            </label>
                            <div x-data="{
                                aboutPreview: '{{ $aboutImage['url'] ?? '' }}',
                                handleAboutChange(e) {
                                    const file = e.target.files[0];
                                    if (file) this.aboutPreview = URL.createObjectURL(file);
                                }
                            }">
                                <div class="relative group w-full bg-linear-to-br from-slate-50 to-slate-100 dark:from-slate-700 dark:to-slate-600 border-2 border-dashed border-slate-200 dark:border-slate-600 rounded-2xl overflow-hidden flex items-center justify-center transition-all duration-300 hover:border-indigo-300 dark:hover:border-indigo-500 hover:shadow-lg"
                                    style="min-height: 160px;">
                                    <template x-if="aboutPreview">
                                        <img :src="aboutPreview" class="max-w-full max-h-full object-contain p-4">
                                    </template>
                                    <template x-if="!aboutPreview">
                                        <div class="text-center p-6">
                                            <i class="fas fa-image text-slate-300 dark:text-slate-500 text-3xl mb-2"></i>
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase">No Image</p>
                                        </div>
                                    </template>
                                    <label class="absolute inset-0 bg-linear-to-t from-indigo-900/80 to-purple-900/60 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center cursor-pointer backdrop-blur-sm">
                                        <div class="text-center transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                            <i class="fas fa-cloud-upload-alt text-xl text-white mb-1"></i>
                                            <span class="block text-white text-[10px] font-black uppercase tracking-widest">Change Image</span>
                                        </div>
                                        <input type="file" name="about_image" class="hidden" accept="image/*"
                                            @change="handleAboutChange">
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
