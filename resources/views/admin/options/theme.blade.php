@extends('layouts.admin')

@section('title', 'Theme Settings')

@push('header_actions')
    <button type="submit" form="theme-form"
        class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-bold rounded-xl shadow-lg text-white bg-linear-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 hover:shadow-xl hover:-translate-y-0.5">
        <i class="fas fa-save mr-2"></i>
        Save
    </button>
@endpush

@section('content')
<div class="space-y-8 animate-fadeInUp">
    <form id="theme-form" action="{{ route('admin.theme.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 overflow-hidden">
            <div class="p-8 space-y-6">
                {{-- Theme Sections --}}
                <div>
                    @if(empty($themeSections))
                        <div class="text-center py-8 bg-slate-50/50 dark:bg-slate-700/30 rounded-xl border border-dashed border-slate-200 dark:border-slate-600">
                            <div class="w-14 h-14 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-info-circle text-slate-400 text-xl"></i>
                            </div>
                            <p class="text-sm text-slate-500 dark:text-slate-400 italic">No theme sections registered.</p>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($themeSections as $sectionKey => $section)
                                @continue($sectionKey == 'navigation')
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
                                <div x-data="{ selected: '{{ $currentValue }}' }">
                                    <label class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2.5">
                                        {{ $section['label'] ?? ucfirst($sectionKey) }}{{ $labelSuffix }}
                                    </label>

                                    <input type="hidden" name="settings[{{ $optionKey }}]" x-model="selected">

                                    <div class="flex flex-wrap gap-3">
                                        @foreach($available as $valueKey => $valueLabel)
                                            <div @click="selected = '{{ $valueKey }}'"
                                                    :class="selected === '{{ $valueKey }}' ? 'ring-1 ring-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 border-indigo-300 dark:border-indigo-600 shadow-sm' : 'border-slate-200 dark:border-slate-600 hover:border-slate-300 dark:hover:border-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                                                    class="w-fit flex items-center gap-3 px-3.5 py-2.5 rounded-lg border cursor-pointer transition-all duration-150 select-none">
                                                <i :class="selected === '{{ $valueKey }}' ? 'fa-solid fa-square-check text-indigo-600 dark:text-indigo-400' : 'fa-regular fa-square text-slate-300 dark:text-slate-500'" class="text-lg shrink-0"></i>
                                                <span class="text-sm font-medium text-slate-700 dark:text-slate-200 leading-tight">{{ $valueLabel }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Female Teacher Photo Settings --}}
        @php
            $hideFemalePhoto = ($options['institute.branding.hide_female_teacher_photo'] ?? '0') === '1';
            $femalePhotoRaw = $options['institute.branding.female_teacher_photo_json'] ?? null;
            $femalePhoto = is_array($femalePhotoRaw)
                ? $femalePhotoRaw
                : (is_string($femalePhotoRaw) && $femalePhotoRaw !== '' ? (json_decode($femalePhotoRaw, true) ?: []) : []);
        @endphp

        <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 overflow-hidden">
            <div class="w-full md:max-w-1/2 p-4 md:p-6 space-y-6">
                {{-- Hide Female Teacher Photo Toggle --}}
                <div class="bg-slate-50/50 dark:bg-slate-700/30 rounded-xl p-5 border border-slate-100 dark:border-slate-600">
                    <div class="flex items-center justify-between gap-4">
                        <label for="institute.branding.hide_female_teacher_photo" class="flex items-start gap-4 cursor-pointer min-w-0 flex-1">
                            <div class="min-w-0">
                                <p class="font-bold text-slate-800 dark:text-slate-200">Hide Female Teacher Photos</p>
                            </div>
                        </label>
                        <label for="institute.branding.hide_female_teacher_photo" class="relative inline-flex items-center shrink-0 cursor-pointer">
                            <input type="hidden" name="settings[institute.branding.hide_female_teacher_photo]" value="0">
                            <input type="checkbox" id="institute.branding.hide_female_teacher_photo"
                                name="settings[institute.branding.hide_female_teacher_photo]" value="1"
                                class="peer sr-only"
                                {{ $hideFemalePhoto ? 'checked' : '' }}>
                            <span class="w-12 h-6 bg-slate-300 dark:bg-slate-600 rounded-full peer-checked:bg-linear-to-r peer-checked:from-pink-500 peer-checked:to-rose-500 transition-all duration-300"></span>
                            <span class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-md transform peer-checked:translate-x-6 peer-checked:shadow-lg transition-all duration-300"></span>
                        </label>
                    </div>

                    {{-- Custom placeholder uploader --}}
                    <div class="mt-5 pt-5 border-t border-slate-200 dark:border-slate-600"
                        x-data="{
                            preview: '{{ $femalePhoto['url'] ?? '' }}',
                            handleFemalePhotoChange(e) {
                                const file = e.target.files[0];
                                if (file) this.preview = URL.createObjectURL(file);
                            },
                            clearCustomPhoto() {
                                this.preview = '';
                                const input = document.getElementById('female_teacher_photo_input');
                                if (input) input.value = '';
                            }
                        }">
                        <label class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <i class="fas fa-image text-pink-500"></i>
                            Custom Female Teacher Photo
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-start">
                            <div class="md:col-span-1">
                                <div class="relative group aspect-square bg-linear-to-br from-slate-50 to-slate-100 dark:from-slate-700 dark:to-slate-600 border-2 border-dashed border-slate-200 dark:border-slate-600 rounded-2xl overflow-hidden flex items-center justify-center transition-all duration-300 hover:border-pink-300 dark:hover:border-pink-500 hover:shadow-lg">
                                    <template x-if="preview">
                                        <img :src="preview" class="max-w-full max-h-full object-contain p-2">
                                    </template>
                                    <template x-if="!preview">
                                        <div class="text-center p-3">
                                            <i class="fas fa-user-circle text-slate-300 dark:text-slate-500 text-3xl mb-1"></i>
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase">No Custom Photo</p>
                                            <p class="text-[9px] text-slate-400 dark:text-slate-500 mt-1 leading-tight">Falls back to <code>images/female-teacher.png</code></p>
                                        </div>
                                    </template>
                                    <label class="absolute inset-0 bg-linear-to-t from-pink-900/80 to-rose-900/60 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center cursor-pointer backdrop-blur-sm">
                                        <div class="text-center transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                            <i class="fas fa-cloud-upload-alt text-xl text-white mb-1"></i>
                                            <span class="block text-white text-[10px] font-black uppercase tracking-widest">Change Photo</span>
                                        </div>
                                        <input type="file" id="female_teacher_photo_input"
                                            name="female_teacher_photo" class="hidden" accept="image/*"
                                            @change="handleFemalePhotoChange">
                                    </label>
                                </div>
                            </div>
                            <div class="md:col-span-2 text-xs text-slate-500 dark:text-slate-400 space-y-2">
                                <p><i class="fas fa-info-circle text-pink-500 mr-1"></i> Upload a square image (PNG/JPG/WebP). It will be converted to WebP for performance.</p>
                                <p><i class="fas fa-eye-slash text-pink-500 mr-1"></i> Used <strong>only</strong> when the toggle above is on and the teacher is marked as female in their profile.</p>
                                <p><i class="fas fa-undo text-pink-500 mr-1"></i> If no custom photo is uploaded, the built-in <code class="text-[10px] bg-slate-200 dark:bg-slate-600 px-1 py-0.5 rounded">public/images/female-teacher.png</code> is used.</p>
                                <button type="button" x-show="preview" @click="clearCustomPhoto"
                                    class="mt-2 inline-flex items-center gap-1.5 text-[11px] font-bold text-pink-600 hover:text-pink-700 uppercase tracking-widest">
                                    <i class="fas fa-times-circle"></i> Clear preview
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
