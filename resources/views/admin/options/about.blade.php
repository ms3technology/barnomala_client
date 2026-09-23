@extends('layouts.admin')

@section('title', 'About Text')

@push('header_actions')
    <button type="submit" form="about-form"
        class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-bold rounded-xl shadow-lg text-white bg-linear-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 hover:shadow-xl hover:-translate-y-0.5">
        <i class="fas fa-save mr-2"></i>
        Save
    </button>
@endpush

@section('content')
<div class="space-y-8 animate-fadeInUp">
    <form id="about-form" action="{{ route('admin.about.update') }}" method="POST">
        @csrf
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

        <div class="max-w-4xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 overflow-hidden">
            <div class="p-6 md:p-8 space-y-6">
                <div>
                    <label class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i class="fas fa-pen-nib text-indigo-500"></i>
                        About Text
                    </label>

                    <x-editor.content-editor
                        name="settings[institute.about.text]"
                        id="institute-about-text-editor"
                        :value="$aboutTextInitial"
                        :old-input="$aboutTextOld"
                        :plain-fallback="$aboutTextPlain"
                        placeholder="Write the About section content..."
                        min-height="320px" />
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
