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
    <form id="theme-form" action="{{ route('admin.theme.update') }}" method="POST">
        @csrf
        {{-- About Text (Lexical rich-text editor) --}}
        <div class="max-w-4xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 overflow-hidden">
            <div class="p-6 space-y-6">
                @php
                    // Load existing About Text value: it may be stored as either
                    // a serialized Lexical JSON string (new) or plain HTML/text (legacy).
                    $aboutTextRaw = $options['institute.about.text'] ?? '';
                    $aboutTextInitial = null;
                    $aboutTextPlain = '';
                    if (is_string($aboutTextRaw) && $aboutTextRaw !== '') {
                        $decoded = json_decode($aboutTextRaw, true);
                        if (is_array($decoded) && isset($decoded['root'])) {
                            $aboutTextInitial = $decoded;
                        } else {
                            $aboutTextPlain = $aboutTextRaw;
                        }
                    }
                    $aboutTextOld = old('settings.institute.about.text');
                @endphp

                <div class="">
                    <label class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i class="fas fa-paragraph text-indigo-500"></i>
                        About Text
                    </label>

                    <x-editor.content-editor
                        name="settings[institute.about.text]"
                        id="institute-about-text-editor"
                        :value="$aboutTextInitial"
                        :old-input="$aboutTextOld"
                        :plain-fallback="$aboutTextPlain"
                        placeholder="Write the About section content..."
                        min-height="240px" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 overflow-hidden">
            <div class="p-6 space-y-6">
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
    </form>
</div>
@endsection
