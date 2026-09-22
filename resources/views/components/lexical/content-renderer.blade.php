@props([
    'state' => null,
    'fallback' => '',
    'wrapperClass' => 'lex-rendered',
    'wrap' => true,
])

@php
    // Build a fallback from a plain-text mirror (e.g. `$post->content`) when
    // the model still has a legacy plain column. This keeps older pages
    // looking reasonable even when no Lexical state has been saved yet.
    $fallbackHtml = $fallback;
    if ($fallbackHtml instanceof \Illuminate\Support\HtmlString) {
        $fallbackHtml = $fallbackHtml->toHtml();
    }
    $fallbackHtml = is_string($fallbackHtml) ? trim($fallbackHtml) : '';

    $rendered = lexical_render($state, $fallbackHtml, false);
    $hasContent = $rendered !== '';
@endphp

@if($hasContent)
    @if($wrap)
        <div class="{{ $wrapperClass }}">
            {!! $rendered !!}
        </div>
    @else
        {!! $rendered !!}
    @endif
@endif

@once
    @push('styles')
        <link rel="stylesheet" href="{{ \Illuminate\Support\Facades\Vite::asset('resources/css/lexical-editor.css') }}">
    @endpush
@endonce
