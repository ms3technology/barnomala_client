@extends('layouts.admin')

@section('title', $isEditing ? 'Edit Speech' : 'Create Speech')

@push('styles')
<style>
    /* Light form palette: flat, no shadows, soft borders. */
    .speech-light-form .lf-control {
        background-color: #ffffff;
        color: #0f172a;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 10px 12px;
        font-size: 15px;
        width: 100%;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        font-family: inherit;
    }
    .speech-light-form .lf-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    .speech-light-form .lf-control::placeholder {
        color: #94a3b8;
    }
    .speech-light-form textarea.lf-control {
        resize: vertical;
    }
    .dark .speech-light-form .lf-control {
        background-color: #1e293b;
        color: #e2e8f0;
        border-color: #334155;
    }
    .dark .speech-light-form .lf-control:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.15);
    }

    .speech-light-form {
        max-width: 100%;
    }

    /* Flat section cards (no shadows). */
    .speech-light-form .lf-section {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 20px;
    }
    .dark .speech-light-form .lf-section {
        background-color: #1e293b;
        border-color: #334155;
    }

    /* Section heading. */
    .speech-light-form .lf-section-title {
        font-size: 14px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 12px;
    }
    .dark .speech-light-form .lf-section-title {
        color: #cbd5e1;
    }

    /* Dropzone: flat with dashed border, no shadow. */
    .speech-light-form .lf-dropzone {
        background-color: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 8px;
        padding: 24px;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s, background-color 0.2s;
    }
    .speech-light-form .lf-dropzone:hover {
        border-color: #2563eb;
        background-color: #f1f5f9;
    }
    .dark .speech-light-form .lf-dropzone {
        background-color: #0f172a;
        border-color: #334155;
    }
</style>
@endpush

@push('header_actions')
<div class="flex items-center gap-5">
    <a href="{{ route('admin.speeches.index') }}" class="inline-flex items-center px-4 py-2.5 border border-slate-200 dark:border-slate-600 text-sm font-bold rounded-lg text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors duration-200">
        <i class="fas fa-arrow-left mr-2"></i> Back
    </a>
    <button type="submit" form="speech-form" class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-bold rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
        <i class="fas fa-save mr-2"></i> {{ $isEditing ? 'Update' : 'Save' }}
    </button>
</div>
@endpush

@section('content')
<div class="speech-light-form animate-fadeInUp" style="background-color:#f8fafc;border-radius:8px;font-family:system-ui,-apple-system,sans-serif;">
    <form id="speech-form" action="{{ $isEditing ? route('admin.speeches.update', $speech) : route('admin.speeches.store') }}" method="POST" enctype="multipart/form-data" x-data="speechForm('{{ $speech->image_url ?? '' }}', {{ $isEditing && $speech->image_json ? 'true' : 'false' }})">
        @csrf
        @if($isEditing) @method('PUT') @endif

        <div class="flex flex-col lg:flex-row gap-4">
            <div class="w-full md:w-2/3 bg-white p-4">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="title" class="block text-sm font-semibold text-slate-700 mb-1.5">Title *</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $speech->title) }}" class="lf-control" placeholder="e.g., Principal" required>
                            @error('title') <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">Speaker Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $speech->name) }}" class="lf-control" placeholder="e.g., Md. Karim Uddin">
                            @error('name') <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Speech Content
                        </label>

                        @php
                            if (old('speech_lexical') !== null) {
                                $lexicalValue = json_decode(old('speech_lexical'), true);
                            } elseif (!empty($speech->speech_lexical)) {
                                $lexicalValue = $speech->speech_lexical;
                            } else {
                                $lexicalValue = null;
                            }
                            $plainFallback = $speech->speech ?? '';
                        @endphp

                        <x-editor.content-editor
                            name="speech_lexical"
                            id="speech-editor"
                            :value="$lexicalValue"
                            placeholder="Write the speech content here..." />

                        {{--
                            Hidden mirror so the existing `speech` required-rule
                            keeps working without code changes. The controller
                            will also overwrite `speech` from the decoded
                            lexical JSON, so this stays in sync.
                        --}}
                        <textarea name="speech"
                                  id="speech"
                                  x-data
                                  x-init="
                                      const mirrorFrom = (serialized) => {
                                          try {
                                              const data = JSON.parse(serialized);
                                              const walk = (n) => {
                                                  let out = '';
                                                  if (n && Array.isArray(n.children)) {
                                                      for (const child of n.children) out += walk(child);
                                                  }
                                                  if (n && n.type === 'text' && typeof n.text === 'string') {
                                                      out += n.text;
                                                  }
                                                  if ((n?.type === 'paragraph' || n?.type === 'heading' || n?.type === 'quote' || n?.type === 'listitem') && out.length) {
                                                      out += '\n\n';
                                                  }
                                                  return out;
                                              };
                                              return walk(data?.root ?? data).trim();
                                          } catch (e) { return ''; }
                                      };
                                      const hidden = $el.closest('form')?.querySelector('input[name=speech_lexical]');
                                      if (hidden) {
                                          const sync = () => { $el.value = mirrorFrom(hidden.value); };
                                          sync();
                                          hidden.addEventListener('input', sync);
                                          new MutationObserver(sync).observe(hidden, { attributes: true, attributeFilter: ['value'] });
                                      }
                                  "
                                  class="hidden">{{ old('speech', $speech->speech) }}</textarea>

                        @error('speech') <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                        @error('speech_lexical') <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="w-full md:w-1/3 space-y-6">
                <div class="bg-white overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                        <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-image text-indigo-500"></i> Speaker Image
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="mb-4 aspect-square rounded-xl overflow-hidden border-2 border-dashed border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 flex items-center justify-center">
                            <template x-if="coverPreview">
                                <img :src="coverPreview" alt="Preview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!coverPreview && existingImage">
                                <img :src="existingImage" alt="Current image" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!coverPreview && !existingImage">
                                <i class="fas fa-image text-3xl text-slate-400"></i>
                            </template>
                        </div>
                        <div @click="$refs.imageInput.click()" class="flex items-center justify-center px-4 py-4 border-2 border-dashed border-slate-200 dark:border-slate-600 rounded-xl cursor-pointer hover:border-indigo-400 transition-all bg-slate-50/50 group">
                            <i class="fas fa-upload mr-3 text-slate-400 group-hover:text-indigo-500"></i>
                            <span class="text-sm font-semibold text-slate-600 dark:text-slate-400" x-text="existingImage ? 'Change image' : 'Choose image'"></span>
                            <input type="file" name="image" accept="image/*" x-ref="imageInput" @change="handleImage($event)" class="hidden">
                        </div>
                        @error('image') <p class="mt-2 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="bg-white overflow-hidden p-6">
                    <div class="flex flex-col gap-6">
                        <div class="flex items-center gap-3">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $speech->is_active ?? true) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-10 h-5.5 bg-slate-200 dark:bg-slate-600 rounded-full peer peer-checked:bg-indigo-600 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border after:rounded-full after:h-4.5 after:w-4.5 after:transition-all peer-checked:after:translate-x-4.5"></div>
                            </label>
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('speechForm', (existingImage, hasExistingImage) => ({
        existingImage: hasExistingImage ? existingImage : '',
        coverPreview: null,
        handleImage(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => { this.coverPreview = e.target.result; };
            reader.readAsDataURL(file);
        }
    }));
});
</script>
@endsection
