@extends('layouts.admin')

@section('title', $isEditing ? 'Edit Post' : 'Create Post')

@push('styles')
<style>
    /* Light form palette: flat, no shadows, soft borders. */
    .post-light-form .lf-control {
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
    .post-light-form .lf-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    .post-light-form .lf-control::placeholder {
        color: #94a3b8;
    }
    .post-light-form textarea.lf-control {
        resize: vertical;
    }
    .dark .post-light-form .lf-control {
        background-color: #1e293b;
        color: #e2e8f0;
        border-color: #334155;
    }
    .dark .post-light-form .lf-control:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.15);
    }

    .post-light-form {
        max-width: 100%;
    }

    /* Flat section cards (no shadows). */
    .post-light-form .lf-section {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 20px;
    }
    .dark .post-light-form .lf-section {
        background-color: #1e293b;
        border-color: #334155;
    }

    /* Section heading. */
    .post-light-form .lf-section-title {
        font-size: 14px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 12px;
    }
    .dark .post-light-form .lf-section-title {
        color: #cbd5e1;
    }

    /* Dropzone: flat with dashed border, no shadow. */
    .post-light-form .lf-dropzone {
        background-color: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 8px;
        padding: 24px;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s, background-color 0.2s;
    }
    .post-light-form .lf-dropzone:hover {
        border-color: #2563eb;
        background-color: #f1f5f9;
    }
    .dark .post-light-form .lf-dropzone {
        background-color: #0f172a;
        border-color: #334155;
    }
</style>
@endpush

@push('header_actions')
<div class="flex items-center gap-5">
    <a href="{{ route('admin.posts.index') }}" class="inline-flex items-center px-4 py-2.5 border border-slate-200 dark:border-slate-600 text-sm font-bold rounded-lg text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors duration-200">
        <i class="fas fa-arrow-left mr-2"></i> Back
    </a>
    <button type="submit" form="post-form" class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-bold rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
        <i class="fas fa-save mr-2"></i> {{ $isEditing ? 'Update' : 'Save' }}
    </button>
</div>
@endpush

@section('content')
<div class="post-light-form animate-fadeInUp" style="background-color:#f8fafc;border-radius:8px;font-family:system-ui,-apple-system,sans-serif;">
    <form id="post-form" action="{{ $isEditing ? route('admin.posts.update', $post) : route('admin.posts.store') }}" method="POST" enctype="multipart/form-data" x-data="postForm('{{ old('type', $post->type) }}', {{ $isEditing ? 'true' : 'false' }}, '{{ $post->image_json['url'] ?? '' }}')">
        @csrf
        @if($isEditing) @method('PUT') @endif

        <div class="bg-white p-4">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="w-full md:w-1/3">
                    <label for="type" class="block text-sm font-semibold text-slate-700 mb-1.5">Post Type *</label>
                    <select name="type" id="type" x-model="type" class="lf-control" required>
                        @foreach($types as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
                    </select>
                    @error('type') <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>
                <div class="w-full md:flex-1">
                    <label for="title" class="block text-sm font-semibold text-slate-700 mb-1.5">Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}" class="lf-control" placeholder="Enter a descriptive title..." required>
                    @error('title') <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-4">
            <div class="w-full md:w-2/3 bg-white p-4">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Content
                        </label>

                        @php
                            if (old('content_lexical') !== null) {
                                $lexicalValue = json_decode(old('content_lexical'), true);
                            } elseif (!empty($post->content_lexical)) {
                                $lexicalValue = $post->content_lexical;
                            } else {
                                $lexicalValue = null;
                            }
                            $plainFallback = $post->content ?? '';
                        @endphp

                        <x-editor.content-editor
                            :value="$lexicalValue"
                            placeholder="Write the full content here..." />

                        {{--
                            Hidden mirror so the existing `content` required-rule
                            (notice / news) keeps working without code changes.
                            The controller will also overwrite `content` from the
                            decoded lexical JSON, so this stays in sync.
                        --}}
                        <textarea name="content"
                                  id="content"
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
                                      const hidden = $el.closest('form')?.querySelector('input[name=content_lexical]');
                                      if (hidden) {
                                          const sync = () => { $el.value = mirrorFrom(hidden.value); };
                                          sync();
                                          hidden.addEventListener('input', sync);
                                          new MutationObserver(sync).observe(hidden, { attributes: true, attributeFilter: ['value'] });
                                      }
                                  "
                                  class="hidden">{{ old('content', $post->content) }}</textarea>

                        @error('content') <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                        @error('content_lexical') <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div x-show="isDownload" x-cloak class="space-y-6">
                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="class_label" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Class / Group (Optional)</label>
                                <input name="class_label" id="class_label" value="{{ old('class_label', $post->class_label) }}" class="block w-full px-4 py-2.5 text-sm rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full md:w-1/3 space-y-6">
                <div x-show="isNews" x-cloak class="bg-white overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50"><h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider flex items-center gap-2"><i class="fas fa-image text-indigo-500"></i> Cover Image</h3></div>
                    <div class="p-6"><div class="mb-4 aspect-video rounded-xl overflow-hidden border-2 border-dashed border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 flex items-center justify-center"><template x-if="coverPreview"><img :src="coverPreview" alt="Cover preview" class="w-full h-full object-cover"></template><template x-if="!coverPreview && existingCover"><img :src="existingCover" alt="Current cover" class="w-full h-full object-cover"></template><template x-if="!coverPreview && !existingCover"><i class="fas fa-camera text-3xl text-slate-400"></i></template></div><div @click="$refs.coverInput.click()" class="flex items-center justify-center px-4 py-4 border-2 border-dashed border-slate-200 dark:border-slate-600 rounded-xl cursor-pointer hover:border-indigo-400 transition-all bg-slate-50/50 group"><i class="fas fa-upload mr-3 text-slate-400 group-hover:text-indigo-500"></i><span class="text-sm font-semibold text-slate-600 dark:text-slate-400" x-text="existingCover ? 'Change cover image' : 'Choose cover image'"></span><input type="file" name="image" accept="image/*" x-ref="coverInput" @change="handleCoverImage($event)" class="hidden"></div>@error('image') <p class="mt-2 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror</div>
                </div>
                <div class="bg-white overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50"><h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider flex items-center gap-2"><i class="fas fa-paperclip text-indigo-500"></i> Attachments</h3></div>
                    <div class="p-6">
                        @if($isEditing && $post->artifacts->count())
                        <div class="mb-5">
                            <div class="space-y-2">
                                @foreach($post->artifacts as $artifact)
                                <div x-data="{ marked: false }" x-show="!marked" class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0 flex-1">
                                        <i class="fas fa-file-alt text-indigo-500 shrink-0"></i><input type="text" name="artifact_names[{{ $artifact->id }}]" value="{{ old('artifact_names.'.$artifact->id, $artifact->file_name) }}" maxlength="255" placeholder="File name" class="block w-full px-3 py-2 text-sm font-semibold rounded-lg border border-slate-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                    </div>
                                    <input type="hidden" name="delete_artifacts[{{ $artifact->id }}]" :value="marked ? 1 : 0">
                                    <button @click="marked = true" type="button" class="w-9 h-9 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors shrink-0" title="Delete file"><i class="fas fa-trash-alt text-xs"></i></button>
                                </div>
                                @endforeach
                                @error('artifact_names.*') <p class="mt-2 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        @endif
                        <template x-if="selectedFiles.length"><div class="mb-5 space-y-2"><template x-for="(file, index) in selectedFiles" :key="index"><div class="flex items-center justify-between gap-3"><div class="flex items-center gap-3 min-w-0 flex-1"><i class="fas fa-file-alt text-indigo-500 shrink-0"></i><input type="text" :name="`artifact_file_names[${index}]`" x-model="file.displayName" maxlength="255" placeholder="File name" class="block w-full px-3 py-2 text-sm font-semibold rounded-lg border border-slate-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"></div><button @click="removeFile(index)" type="button" class="w-9 h-9 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors shrink-0" title="Remove file"><i class="fas fa-times"></i></button></div></template></div></template>
                        <div @click="$refs.attachmentsInput.click()" class="flex flex-col items-center justify-center px-6 py-8 border-2 border-dashed border-slate-200 dark:border-slate-600 rounded-xl cursor-pointer hover:border-indigo-400 transition-all bg-slate-50/50 group"><i class="fas fa-cloud-upload-alt text-2xl text-slate-400 group-hover:text-indigo-500 mb-2"></i><p class="text-sm font-semibold text-slate-600 dark:text-slate-400">Click to add files</p><p class="text-xs text-slate-400 mt-1">Maximum 20 MB per file.</p><input type="file" name="artifacts[]" multiple x-ref="attachmentsInput" @change="handleAttachments($event)" class="hidden"></div>@error('artifacts.*') <p class="mt-2 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>               

                <div class="bg-white overflow-hidden p-6">
                    <div class="flex flex-col gap-6">
                        <div class="md:flex-1 md:max-w-50"><label for="published_at" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Publication Date *</label><input type="date" name="published_at" id="published_at" value="{{ old('published_at', optional($post->published_at)->format('Y-m-d') ?: now()->format('Y-m-d')) }}" class="block w-full px-3 py-2.5 text-sm font-bold rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm" required>@error('published_at') <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror</div>
                        <div class="flex items-center gap-3"><span class="text-sm font-semibold text-slate-700 dark:text-slate-300">Published</span><label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $post->is_active) ? 'checked' : '' }} class="sr-only peer"><div class="w-10 h-5.5 bg-slate-200 dark:bg-slate-600 rounded-full peer peer-checked:bg-indigo-600 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border after:rounded-full after:h-4.5 after:w-4.5 after:transition-all peer-checked:after:translate-x-4.5"></div></label></div>
                        <div x-show="isNotice" x-cloak class="flex items-center gap-3"><span class="text-sm font-semibold text-slate-700 dark:text-slate-300">Urgent Notice</span><input type="checkbox" name="is_urgent" value="1" {{ old('is_urgent', $post->is_urgent) ? 'checked' : '' }} class="h-5 w-5 rounded border-slate-300 text-red-500 focus:ring-red-500"></div>
                        <div x-show="isNews" x-cloak class="flex items-center gap-3"><span class="text-sm font-semibold text-slate-700 dark:text-slate-300">Featured</span><input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $post->is_featured) ? 'checked' : '' }} class="h-5 w-5 rounded border-slate-300 text-amber-500 focus:ring-amber-500"></div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('postForm', (initialType, isEditing, existingCover) => ({
        type: initialType,
        existingCover,
        coverPreview: null,
        selectedFiles: [],
        get isNotice() { return this.type === 'notice'; },
        get isNews() { return this.type === 'news'; },
        get isDownload() { return !this.isNotice && !this.isNews; },
        handleAttachments(event) {
            Array.from(event.target.files).forEach(file => {
                if (file.size <= 20 * 1024 * 1024 && !this.selectedFiles.some(item => item.name === file.name && item.size === file.size)) {
                    file.displayName = file.name;
                    this.selectedFiles.push(file);
                }
            });
            this.syncInput();
        },
        removeFile(index) { this.selectedFiles.splice(index, 1); this.syncInput(); },
        syncInput() {
            const input = this.$refs.attachmentsInput;
            if (!input) return;
            const dataTransfer = new DataTransfer();
            this.selectedFiles.forEach(file => dataTransfer.items.add(file));
            input.files = dataTransfer.files;
        },
        handleCoverImage(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = event => { this.coverPreview = event.target.result; };
            reader.readAsDataURL(file);
        }
    }));
});
</script>
@endsection
