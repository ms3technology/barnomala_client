@extends('layouts.admin')

@section('title', 'Add News')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800">Add New News</h1>
    <a href="{{ route('admin.news.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:opacity-90 transition-opacity text-sm font-bold shadow-sm">
        <i class="fas fa-arrow-left mr-2"></i> Back
    </a>
</div>

<form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-black text-gray-700 uppercase tracking-widest mb-2" for="title">Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" 
                               class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:border-accent focus:ring-4 focus:ring-accent/5 transition-all text-lg font-bold" 
                               placeholder="Enter News Title" required>
                        @error('title') <p class="mt-2 text-sm text-red-600 font-bold italic">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-black text-gray-700 uppercase tracking-widest mb-2" for="summary">Summary</label>
                        <textarea name="summary" id="summary" rows="3" 
                                  class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:border-accent focus:ring-4 focus:ring-accent/5 transition-all" 
                                  placeholder="Short summary for preview (Optional)">{{ old('summary') }}</textarea>
                        @error('summary') <p class="mt-2 text-sm text-red-600 font-bold italic">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-black text-gray-700 uppercase tracking-widest mb-2" for="content">Full Content</label>
                        <textarea name="content" id="content" rows="12" 
                                  class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:border-accent focus:ring-4 focus:ring-accent/5 transition-all text-gray-800 leading-relaxed" 
                                  placeholder="Write full news article content here..." required>{{ old('content') }}</textarea>
                        @error('content') <p class="mt-2 text-sm text-red-600 font-bold italic">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-black text-gray-900 mb-6 uppercase tracking-wider flex items-center">
                    <i class="fas fa-paperclip text-accent mr-3"></i> Attachments
                </h3>
                <div class="space-y-4">
                    <label class="block text-sm font-black text-gray-700 uppercase tracking-widest mb-2">Upload Files (Images, PDF, Doc)</label>
                    <input type="file" name="artifacts[]" multiple 
                           class="w-full px-5 py-4 rounded-xl border-2 border-dashed border-gray-200 hover:border-accent transition-colors bg-gray-50/50 cursor-pointer">
                    <p class="text-xs text-gray-400 font-bold flex items-center">
                        <i class="fas fa-info-circle mr-2"></i> Max size: 10MB per file. Multi-select allowed.
                    </p>
                    @error('artifacts.*') <p class="mt-2 text-sm text-red-600 font-bold italic">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Sidebar Options -->
        <div class="space-y-6">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-black text-gray-900 mb-6 uppercase tracking-wider">Publishing</h3>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-black text-gray-700 uppercase tracking-widest mb-2" for="published_at">Publish Date</label>
                        <input type="date" name="published_at" id="published_at" value="{{ old('published_at', date('Y-m-d')) }}" 
                               class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:border-accent focus:ring-4 focus:ring-accent/5 transition-all font-bold">
                        @error('published_at') <p class="mt-2 text-sm text-red-600 font-bold italic">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-4">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} 
                                   class="w-5 h-5 rounded border-gray-300 text-accent focus:ring-accent/30 transition-all">
                            <span class="text-sm font-bold text-gray-700 group-hover:text-accent transition-colors">Visible to Public</span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} 
                                   class="w-5 h-5 rounded border-gray-300 text-yellow-500 focus:ring-yellow-500/30 transition-all">
                            <span class="text-sm font-bold text-gray-700 group-hover:text-yellow-600 transition-colors">Featured Post</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-4 bg-accent text-white rounded-xl shadow-lg shadow-accent/20 hover:opacity-90 transition-all text-sm font-black uppercase tracking-[0.2em]">
                        Save News Item
                    </button>
                </div>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-black text-gray-900 mb-6 uppercase tracking-wider">Cover Image</h3>
                <div class="space-y-4">
                    <input type="file" name="image" id="image" accept="image/*" 
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-accent/10 file:text-accent hover:file:bg-accent/20 transition-all">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider italic leading-relaxed">
                        Recommended size: 1280x720px (16:9). Max: 2MB.
                    </p>
                    @error('image') <p class="mt-2 text-sm text-red-600 font-bold italic">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
