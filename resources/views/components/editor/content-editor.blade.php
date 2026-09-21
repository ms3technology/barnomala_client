@props([
    'name' => 'content_lexical',
    'id' => 'lexical-editor',
    'value' => null,
    'placeholder' => 'Start writing the body...',
    'required' => false,
    'minHeight' => '320px',
    'oldInput' => null,
    'plainFallback' => '',
])

@php
    // The editor needs an OBJECT-state to feed `parseEditorState`. We may
    // receive either an array (preferred) or a string (already-encoded JSON).
    // Normalising to a plain array here means the consumer can blindly
    // `JSON.parse` the rendered attribute once and get a usable seed.
    $initialObject = $oldInput !== null
        ? (is_string($oldInput) ? json_decode($oldInput, true) : $oldInput)
        : (is_array($value) ? $value : (is_string($value) ? json_decode($value, true) : null));

    if (!is_array($initialObject)) {
        $initialObject = null;
    }

    // Stable ids for the editor wrapper + hidden input.
    $hiddenId = $id . '-input';
    $wrapperId = $id . '-wrapper';
@endphp

<div id="{{ $wrapperId }}" data-lexical-wrapper data-lexical-required="{{ $required ? '1' : '0' }}"
     class="lexical-component"
     @if($required) x-data="{ required: true }" @endif>
    <div class="lexical-toolbar" role="toolbar" aria-label="Formatting toolbar">
        <button type="button" data-command="bold" title="Bold (Ctrl+B)">Bold</button>
        <button type="button" data-command="italic" title="Italic (Ctrl+I)">Italic</button>
        <button type="button" data-command="underline" title="Underline (Ctrl+U)">Underline</button>
        <button type="button" data-command="strikethrough" title="Strikethrough">Strike</button>
        <button type="button" data-command="code" title="Inline code">Code</button>
        <span class="lexical-toolbar-sep" aria-hidden="true"></span>
        <button type="button" data-command="h1" title="Heading 1">H1</button>
        <button type="button" data-command="h2" title="Heading 2">H2</button>
        <button type="button" data-command="h3" title="Heading 3">H3</button>
        <button type="button" data-command="paragraph" title="Paragraph">P</button>
        <button type="button" data-command="quote" title="Block quote">Quote</button>
        <span class="lexical-toolbar-sep" aria-hidden="true"></span>
        <button type="button" data-command="ul" title="Bulleted list">UL</button>
        <button type="button" data-command="ol" title="Numbered list">OL</button>
    </div>

    <div id="{{ $id }}"
         data-lexical-editor
         data-lexical-target="#{{ $hiddenId }}"
         data-lexical-toolbar="[data-command]"
         @if($initialObject)
             data-lexical-initial='@json($initialObject)'
         @endif
         @if(filled($plainFallback))
             data-lexical-plain-fallback="{{ $plainFallback }}"
         @endif
         contenteditable="true"
         role="textbox"
         aria-multiline="true"
         aria-label="Post content"
         class="lexical-editor"
         data-placeholder="{{ $placeholder }}"
         style="min-height: {{ $minHeight }};"></div>

    <input type="hidden"
           name="{{ $name }}"
           id="{{ $hiddenId }}"
           value="{{ is_string($oldInput) ? $oldInput : ($initialObject ? json_encode($initialObject) : '') }}"
           @if($required) :required="required" @endif>

    @if($required)
        <p class="mt-2 text-[11px] text-slate-500 dark:text-slate-400 font-medium">
            The hidden field above is what Laravel receives. It is updated automatically on every keystroke.
        </p>
    @endif
</div>

@once
    @push('scripts')
        @vite('resources/js/lexical-editor.js')
    @endpush
@endonce
