@extends('layouts.admin')

@section('title', 'Add Gallery Item')

@push('header_actions')
<button type="submit" form="gallery-form" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
    <i class="fas fa-plus mr-2"></i>
    Save Item
</button>
@endpush

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800">Add New Gallery Item</h1>
    <a href="{{ route('admin.gallery.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:opacity-90 transition-opacity text-sm font-bold">
        <i class="fas fa-arrow-left mr-2"></i> Back
    </a>
</div>

<form id="gallery-form" action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-3 space-y-4">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="type">Type</label>
                            <select name="type" id="type" onchange="toggleTypeFields()" 
                                    class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all font-bold">
                                <option value="photo" {{ old('type') == 'photo' ? 'selected' : '' }}>Photo</option>
                                <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Video</option>
                            </select>
                            @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="title">Title</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" 
                                   class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all font-bold" 
                                   placeholder="Enter Item Title" required>
                            @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div id="video-field" class="{{ old('type') == 'video' ? '' : 'hidden' }} space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="video_url">Video URL (YouTube/Vimeo)</label>
                            <input type="url" name="video_url" id="video_url" value="{{ old('video_url') }}" 
                                   class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all text-sm" 
                                   placeholder="https://www.youtube.com/watch?v=...">
                            @error('video_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="p-4 bg-slate-50 rounded-xl border-2 border-dashed border-slate-200">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2" for="video_file">Or Upload Video File</label>
                            <input type="file" name="video_file" id="video_file" accept="video/*"
                                   class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="mt-2 text-[10px] text-gray-400 italic">Max size: 20MB. Formats: MP4, MOV, OGG.</p>
                            @error('video_file') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="category">Category</label>
                            <input type="text" name="category" id="category" value="{{ old('category') }}" 
                                   class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all text-sm" 
                                   placeholder="e.g. Campus, Sports, Annual Day">
                            @error('category') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="date">Event Date</label>
                            <input type="date" name="date" id="date" value="{{ old('date') }}" 
                                   class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all text-sm">
                            @error('date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="description">Description</label>
                        <textarea name="description" id="description" rows="4" 
                                  class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all text-sm" 
                                  placeholder="Provide some context for the item...">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div id="photo-field" class="lg:col-span-1 space-y-4 {{ old('type', 'photo') == 'photo' ? '' : 'hidden' }}">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Photo Upload</label>
                <div class="space-y-4">
                    <div class="relative group">
                        <div class="aspect-square bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl overflow-hidden flex items-center justify-center transition-all group-hover:bg-gray-100 group-hover:border-indigo-300">
                            <img id="preview" src="#" alt="Preview" class="hidden w-full h-full object-cover">
                            <div id="placeholder" class="text-center p-4">
                                <i class="fas fa-image text-3xl text-gray-300 mb-2"></i>
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Select Image</p>
                            </div>
                        </div>
                        <input type="file" name="image" id="image" class="absolute inset-0 opacity-0 cursor-pointer" onchange="previewImage(this)">
                    </div>
                    @error('image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    <p class="text-[10px] text-gray-400 leading-relaxed italic">
                        Max size: 2MB. Format: JPG, PNG, WEBP.
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    function toggleTypeFields() {
        const type = document.getElementById('type').value;
        const photoField = document.getElementById('photo-field');
        const videoField = document.getElementById('video-field');
        
        if (type === 'photo') {
            photoField.classList.remove('hidden');
            videoField.classList.add('hidden');
        } else {
            photoField.classList.add('hidden');
            videoField.classList.remove('hidden');
        }
    }

    function previewImage(input) {
        const preview = document.getElementById('preview');
        const placeholder = document.getElementById('placeholder');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection