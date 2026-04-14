@extends('layouts.admin')

@section('title', 'Add News')

@push('header_actions')
<div class="flex items-center gap-5">
    <button>
        <a href="{{ route('admin.news.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Back
        </a>
    </button>
    <button type="submit" form="news-form" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
        <i class="fas fa-plus mr-2"></i>
        Save News
    </button>
</div>
@endpush

@section('content')
<form id="news-form" action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-3 space-y-4">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="title">Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" 
                               class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-accent focus:ring-2 focus:ring-accent/30 transition-all font-bold" 
                               placeholder="Enter News Title" required>
                        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="summary">Summary</label>
                        <textarea name="summary" id="summary" rows="2" 
                                  class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-accent focus:ring-2 focus:ring-accent/30 transition-all text-sm" 
                                  placeholder="Short preview...">{{ old('summary') }}</textarea>
                        @error('summary') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="content">Full Content</label>
                        <textarea name="content" id="content" rows="8" 
                                  class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-accent focus:ring-2 focus:ring-accent/30 transition-all text-sm" 
                                  placeholder="Write news article..." required>{{ old('content') }}</textarea>
                        @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wider flex items-center">
                    <i class="fas fa-paperclip text-accent mr-2\"></i> Attachments
                </h3>
                <div class="space-y-2">
                    <input type="file" name="artifacts[]" multiple 
                           class="w-full px-3 py-2 rounded-lg border-2 border-dashed border-gray-200 hover:border-accent transition-colors text-xs cursor-pointer">
                    <p class="text-xs text-gray-400">Max 10MB per file. Multi-select allowed.</p>
                    @error('artifacts.*') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Sidebar Options -->
        <div class="space-y-4">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wider\">Publishing</h3>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="published_at">Date</label>
                        <input type="date" name="published_at" id="published_at" value="{{ old('published_at', date('Y-m-d')) }}" 
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-accent focus:ring-2 focus:ring-accent/30 transition-all text-sm font-bold">
                        @error('published_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} 
                               class="w-4 h-4 rounded border-gray-300 text-accent focus:ring-accent/30 transition-all">
                        <span class="text-xs font-bold text-gray-700 group-hover:text-accent transition-colors">Visible</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} 
                               class="w-4 h-4 rounded border-gray-300 text-yellow-500 focus:ring-yellow-500/30 transition-all">
                        <span class="text-xs font-bold text-gray-700 group-hover:text-yellow-600 transition-colors">Featured</span>
                    </label>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-sm font-bold text-gray-900 mb-3 uppercase tracking-wider">Cover Image</h3>
                <div class="space-y-2">
                    <input type="file" name="image" id="image" accept="image/*" 
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-xs cursor-pointer">
                    <p class="text-xs text-gray-400">1280x720px, Max 2MB</p>
                    @error('image') <p class="mt-2 text-sm text-red-600 font-bold italic">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
