@extends('layouts.admin')

@section('title', 'Branding Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-8 text-gray-900">
            <h1 class="text-2xl font-bold text-slate-900 mb-6 flex items-center">
                <i class="fas fa-palette mr-3 text-indigo-600"></i>
                Branding & Visuals
            </h1>

            <form action="{{ route('admin.branding.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <!-- Identity Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <label class="block text-sm font-bold text-slate-700">Institute Display Name</label>
                        <input type="text" name="institute_branding_name" value="{{ $options['institute.branding.name'] ?? '' }}" 
                               class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

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
                                <input type="text" name="institute_branding_accent_color" x-model="selectedColor"
                                       class="flex-1 border-slate-300 rounded-lg text-sm font-mono uppercase">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Banner Section -->
                <div class="space-y-6 pt-6 border-t border-slate-100">
                    <h2 class="text-lg font-bold text-slate-900">Header Visuals</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Logo Upload</label>
                            <div class="relative group aspect-square max-w-37.5 bg-slate-50 border-2 border-dashed border-slate-300 rounded-xl overflow-hidden flex items-center justify-center">
                                @php
                                    $logo = json_decode($options['institute.branding.logo_json'] ?? '{}', true);
                                @endphp
                                @if(isset($logo['url']))
                                    <img src="{{ $logo['url'] }}" class="max-w-full max-h-full object-contain p-4">
                                @else
                                    <i class="fas fa-image text-slate-300 text-3xl"></i>
                                @endif
                                <label class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer">
                                    <span class="text-white text-xs font-bold">Replace Logo</span>
                                    <input type="file" name="logo" class="hidden" accept="image/*">
                                </label>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <label class="block text-sm font-bold text-slate-700">Header Design Style</label>
                            <select name="institute_branding_banner_type" class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="banner_only" {{ ($options['institute.branding.banner_type'] ?? '') == 'banner_only' ? 'selected' : '' }}>Full Width Banner Only</option>
                                <option value="banner_with_overlay" {{ ($options['institute.branding.banner_type'] ?? '') == 'banner_with_overlay' ? 'selected' : '' }}>Banner with Text Overlay</option>
                                <option value="banner_split" {{ ($options['institute.branding.banner_type'] ?? '') == 'banner_split' ? 'selected' : '' }}>Split: Image Left / Info Right</option>
                                <option value="info_only" {{ ($options['institute.branding.banner_type'] ?? '') == 'info_only' ? 'selected' : '' }}>No Banner (Identity & Contacts Only)</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="block text-sm font-bold text-slate-700">Main Banner Image</label>
                        <div class="relative group aspect-21/9 w-full bg-slate-50 border-2 border-dashed border-slate-300 rounded-xl overflow-hidden flex items-center justify-center">
                            @php
                                $banner = json_decode($options['institute.branding.banner_json'] ?? '{}', true);
                            @endphp
                            @if(isset($banner['url']))
                                <img src="{{ $banner['url'] }}" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-image text-slate-300 text-5xl"></i>
                            @endif
                            <label class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer">
                                <div class="text-center">
                                    <i class="fas fa-cloud-upload-alt text-2xl text-white mb-2"></i>
                                    <p class="text-white text-sm font-bold">Upload New Banner</p>
                                </div>
                                <input type="file" name="banner" class="hidden" accept="image/*">
                            </label>
                        </div>
                        <p class="text-xs text-slate-400">Recommended size: 1920x800px. Used in Full Width and Overlay styles.</p>
                    </div>
                </div>

                <div class="pt-8 flex justify-end">
                    <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                        <i class="fas fa-save mr-2"></i> Update Branding
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
