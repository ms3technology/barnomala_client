@extends('layouts.admin')

@section('title', 'Branding Settings')

@push('header_actions')
<button type="submit" form="branding-form" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
    <i class="fas fa-save mr-2"></i>
    Save Branding
</button>
@endpush

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-8 text-gray-900">
            <h1 class="text-2xl font-bold text-slate-900 mb-6 flex items-center">
                <i class="fas fa-palette mr-3 text-indigo-600"></i>
                Branding & Visuals
            </h1>

            <form id="branding-form" action="{{ route('admin.branding.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <!-- Identity Section -->
                <div class="grid grid-cols-1 md:grid-cols-1 gap-8">
                    <div class="space-y-4">
                        <label class="block text-sm font-bold text-slate-700">Accent Color</label>
                        <div x-data="{ 
                            selectedColor: '{{ $options['institute.branding.accent_color'] ?? '#4F46E5' }}',
                            colors: ['#4F46E5', '#2563EB', '#059669', '#DC2626', '#7C3AED', '#D97706', '#0891B2', '#4B5563']
                        }">
                            <div class="flex flex-wrap gap-3 mb-3">
                                <template x-for="color in colors" :key="color">
                                    <button type="button" 
                                            @click="selectedColor = color"
                                            :style="{ backgroundColor: color }"
                                            class="w-8 h-8 rounded-full border-2 transition-transform hover:scale-110"
                                            :class="selectedColor === color ? 'border-slate-900 scale-110' : 'border-transparent'">
                                    </button>
                                </template>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="color" x-model="selectedColor" class="w-10 h-10 border-none p-0 cursor-pointer">
                                <input type="text" name="settings[institute.branding.accent_color]" x-model="selectedColor"
                                       class="flex-1 border-slate-300 rounded-lg text-sm font-mono uppercase">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Banner Section -->
                @php
                    $logo = json_decode($options['institute.branding.logo_json'] ?? '{}', true);
                    $bannerData = json_decode($options['institute.branding.banner_json'] ?? '{}', true);
                @endphp
                <div class="space-y-6 pt-6 border-t border-slate-100" x-data="{ 
                    bannerPreview: '{{ $bannerData['url'] ?? '' }}',
                    handleBannerChange(e) {
                        const file = e.target.files[0];
                        if (file) {
                            this.bannerPreview = URL.createObjectURL(file);
                        }
                    }
                }">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-900">Header Visuals</h2>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Recommended: 1920x450px</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                        <!-- Logo (3 cols) -->
                        <div class="md:col-span-3 space-y-3 w-48">
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest">Logo</label>
                            <div class="relative group aspect-square bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl overflow-hidden flex items-center justify-center transition-all hover:border-indigo-300" x-data="{ 
                                logoPreview: '{{ isset($logo['url']) ? $logo['url'] : '' }}',
                                handleLogoChange(e) {
                                    const file = e.target.files[0];
                                    if (file) this.logoPreview = URL.createObjectURL(file);
                                }
                            }">
                                <template x-if="logoPreview">
                                    <img :src="logoPreview" class="max-w-full max-h-full object-contain p-4">
                                </template>
                                <template x-if="!logoPreview">
                                    <i class="fas fa-image text-slate-300 text-2xl"></i>
                                </template>
                                <label class="absolute inset-0 bg-indigo-900/60 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center cursor-pointer backdrop-blur-sm">
                                    <span class="text-white text-[10px] font-black uppercase tracking-widest">Change Logo</span>
                                    <input type="file" name="logo" class="hidden" accept="image/*" @change="handleLogoChange">
                                </label>
                            </div>
                        </div>

                        <!-- Banner (9 cols) -->
                        <div class="md:col-span-9 space-y-3">
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest">Main Banner</label>
                            <div class="relative group min-h-37.5 w-full bg-slate-100 border-2 border-dashed border-slate-200 rounded-2xl overflow-hidden flex items-center justify-center transition-all hover:border-indigo-300">
                                <template x-if="bannerPreview">
                                    <img :src="bannerPreview" class="w-full h-auto object-contain max-h-100">
                                </template>
                                <template x-if="!bannerPreview">
                                    <div class="text-center">
                                        <i class="fas fa-mountain text-slate-300 text-4xl mb-2"></i>
                                        <p class="text-slate-400 text-[10px] font-bold uppercase">No Banner Set</p>
                                    </div>
                                </template>
                                <label class="absolute inset-0 bg-indigo-900/60 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center cursor-pointer backdrop-blur-sm">
                                    <div class="text-center transform translate-y-4 group-hover:translate-y-0 transition-transform">
                                        <i class="fas fa-cloud-upload-alt text-2xl text-white mb-2"></i>
                                        <p class="text-white text-[10px] font-black uppercase tracking-widest">Update Banner Image</p>
                                    </div>
                                    <input type="file" name="banner" class="hidden" accept="image/*" @change="handleBannerChange">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest">Header Style</label>
                            <select name="settings[institute.branding.banner_type]" class="w-full border-slate-200 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm font-bold py-3">
                                <option value="banner_only" {{ ($options['institute.branding.banner_type'] ?? '') == 'banner_only' ? 'selected' : '' }}>Standard Full Banner</option>
                                <option value="banner_with_overlay" {{ ($options['institute.branding.banner_type'] ?? '') == 'banner_with_overlay' ? 'selected' : '' }}>Banner with Text Overlay</option>
                                <option value="banner_split" {{ ($options['institute.branding.banner_type'] ?? '') == 'banner_split' ? 'selected' : '' }}>Split (Image Left / Info Right)</option>
                                <option value="info_only" {{ ($options['institute.branding.banner_type'] ?? '') == 'info_only' ? 'selected' : '' }}>No Banner (Identity Only)</option>
                            </select>
                        </div>

                        <!-- Header Background Color -->
                        <div class="space-y-3">
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest">Header Background</label>
                            <div class="flex items-center gap-4 p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                                <input type="color" name="settings[institute.branding.header_bg]" value="{{ $options['institute.branding.header_bg'] ?? '#ffffff' }}" 
                                       class="h-8 w-16 rounded cursor-pointer border-none bg-transparent">
                                <span class="text-xs font-mono font-bold text-slate-600 uppercase">{{ $options['institute.branding.header_bg'] ?? '#ffffff' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                            </label>
                        </div>
                        <p class="text-xs text-slate-400">Recommended size: 1920x800px. Used in Full Width and Overlay styles.</p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
