@extends('layouts.admin')

@section('title', 'Edit Notice')

@push('header_actions')
<button type="submit" form="notice-form" class="inline-flex items-center px-6 py-2.5 text-sm font-bold rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
    <i class="fas fa-save mr-2"></i>
    Update Notice
</button>
@endpush

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="{{ route('admin.notices.index') }}" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors flex items-center mb-2">
            <i class="fas fa-arrow-left mr-2"></i> Back to Notices
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 max-w-5xl">
    <form id="notice-form" action="{{ route('admin.notices.update', $notice) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="space-y-8">
            <!-- Main Content Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Left: Content -->
                <div class="md:col-span-2 space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Notice Title *</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $notice->title ?? '') }}" 
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" 
                               placeholder="Enter a descriptive title..." required>
                        @error('title') <p class="text-xs text-red-500 mt-2 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="content" class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Detailed Content *</label>
                        <textarea name="content" id="content" rows="12" 
                                  class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" 
                                  placeholder="Write the full notice content here..." required>{{ old('content', $notice->content ?? '') }}</textarea>
                        @error('content') <p class="text-xs text-red-500 mt-2 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Right: Metadata & Options -->
                <div class="space-y-6 bg-slate-50 p-6 rounded-xl border border-slate-100">
                    <div>
                        <label for="published_at" class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Publication Date *</label>
                        <input type="date" name="published_at" id="published_at" 
                               value="{{ old('published_at', isset($notice) && $notice->published_at ? $notice->published_at->format('Y-m-d') : now()->format('Y-m-d')) }}" 
                               class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                        @error('published_at') <p class="text-xs text-red-500 mt-2 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-4 border-t border-slate-200">
                        <label class="block text-sm font-bold text-slate-700 mb-3 uppercase tracking-wide">Status & Visiblity</label>
                        
                        <div class="space-y-4">
                            <label class="flex items-start cursor-pointer group">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $notice->is_active ?? true) ? 'checked' : '' }} 
                                           class="w-4 h-4 text-indigo-600 bg-white border-slate-300 rounded focus:ring-indigo-500 focus:ring-2 transition-colors">
                                </div>
                                <div class="ml-3 text-sm">
                                    <span class="font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">Published</span>
                                    <p class="text-xs text-slate-500">Visible to public</p>
                                </div>
                            </label>

                            <label class="flex items-start cursor-pointer group">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="is_urgent" id="is_urgent" value="1" {{ old('is_urgent', $notice->is_urgent ?? false) ? 'checked' : '' }} 
                                           class="w-4 h-4 text-red-600 bg-white border-slate-300 rounded focus:ring-red-500 focus:ring-2 transition-colors">
                                </div>
                                <div class="ml-3 text-sm">
                                    <span class="font-bold text-slate-700 group-hover:text-red-600 transition-colors">Urgent Notice</span>
                                    <p class="text-xs text-slate-500">Highlights in red</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-200">
                        <label class="text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide flex items-center">
                            <i class="fas fa-paperclip mr-2 text-indigo-500"></i> Attachments
                        </label>
                        
                        @if(isset($notice) && $notice->artifacts->count())
                            <div class="bg-indigo-50/50 border border-indigo-100 rounded-lg p-3 mb-4">
                                <p class="text-xs font-semibold text-indigo-900 mb-2 uppercase tracking-wider">Current Files:</p>
                                <div class="space-y-2">
                                    @foreach($notice->artifacts as $artifact)
                                        <div class="flex items-center justify-between bg-white p-2.5 rounded-lg border border-indigo-100 shadow-sm group">
                                            <div class="flex items-center truncate mr-2">
                                                <i class="fas fa-file text-indigo-300 mr-2 shrink-0"></i>
                                                <span class="text-xs font-medium text-slate-700 truncate" title="{{ $artifact->file_name }}">
                                                    {{ $artifact->file_name }}
                                                </span>
                                            </div>
                                            <div class="flex items-center shrink-0">
                                                <span class="text-[10px] text-slate-400 mr-3">{{ round($artifact->file_size / 1024, 1) }}KB</span>
                                                <button type="button" onclick="document.getElementById('delete_artifact_{{ $artifact->id }}').value = '1'; this.parentElement.parentElement.classList.add('hidden')" class="text-slate-400 hover:text-red-600 transition-colors p-1" title="Remove File">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <input type="hidden" id="delete_artifact_{{ $artifact->id }}" name="delete_artifacts[{{ $artifact->id }}]" value="0">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="relative">
                            <input type="file" name="artifacts[]" id="artifacts" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.png,.zip" 
                                   class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-slate-300 rounded-lg bg-white">
                        </div>
                        <p class="text-[11px] text-slate-500 mt-2 font-medium leading-relaxed">Allowed: PDF, DOC, XLS, JPG, PNG, ZIP. Max 50MB.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
