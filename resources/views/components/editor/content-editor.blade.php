@props([
    'name' => 'content_lexical',
    'id' => 'lexical-editor',
    'value' => null,
    'placeholder' => 'Start writing the body...',
    'required' => false,
    'minHeight' => '320px',
    'oldInput' => null,
    'plainFallback' => '',
    'uploadUrl' => null,
    'csrf' => null,
])

@php
    // Normalise the initial state.
    $initialObject = $oldInput !== null
        ? (is_string($oldInput) ? json_decode($oldInput, true) : $oldInput)
        : (is_array($value) ? $value : (is_string($value) ? json_decode($value, true) : null));

    if (!is_array($initialObject)) {
        $initialObject = null;
    }

    $hiddenId = $id . '-input';
    $toolbarId = $id . '-toolbar';

    $uploadUrl = $uploadUrl ?: route('admin.editor.upload-image', [], false);
    $csrf = $csrf ?: csrf_token();
@endphp

<div
    id="{{ $id }}-wrapper"
    data-lexical-wrapper
    class="lex-component lex-playground"
>
    {{-- ── Toolbar host ────────────────────────────────────────────────────── --}}
    <div
        id="{{ $toolbarId }}"
        data-lexical-toolbar
        class="lex-toolbar-host"
        role="toolbar"
        aria-label="Editor toolbar"
    ></div>

    {{-- ── Editable surface ────────────────────────────────────────────────── --}}
    <div
        id="{{ $id }}"
        data-lexical-editor
        data-lexical-target="#{{ $hiddenId }}"
        @if($uploadUrl) data-upload-url="{{ $uploadUrl }}" @endif
        @if($csrf) data-csrf="{{ $csrf }}" @endif
        @if($initialObject) data-lexical-initial='@json($initialObject)' @endif
        @if(filled($plainFallback)) data-lexical-plain-fallback="{{ $plainFallback }}" @endif
        contenteditable="true"
        role="textbox"
        aria-multiline="true"
        aria-label="Post content"
        class="lex-content"
        data-placeholder="{{ $placeholder }}"
        style="min-height: {{ $minHeight }};"
    ></div>

    {{-- ── Hidden input that Laravel reads ─────────────────────────────────── --}}
    <input
        type="hidden"
        name="{{ $name }}"
        id="{{ $hiddenId }}"
        value="{{ is_string($oldInput) ? $oldInput : ($initialObject ? json_encode($initialObject) : '') }}"
        @if($required) required @endif
    >
</div>

@once
    @push('styles')
        <link rel="stylesheet" href="{{ \Illuminate\Support\Facades\Vite::asset('resources/css/lexical-editor.css') }}">
    @endpush
    @push('scripts')
        @vite('resources/js/lexical/index.js')
    @endpush
@endonce
