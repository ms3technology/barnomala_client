@extends('layouts.admin')

@section('title', 'Edit Gallery Item')

@push('header_actions')
<div class="flex items-center gap-5">
    <button>
        <a href="{{ route('admin.gallery.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Back
        </a>
    </button>
    <button type="submit" form="gallery-form" class="inline-flex items-center px-6 py-2 border border-transparent text-sm rounded-lg shadow-md text-white bg-indigo-600 hover:bg-indigo-700 transition-all font-bold">
        <i class="fas fa-save mr-2"></i>
        Update Item
    </button>
</div>
@endpush

@section('content')
<form id="gallery-form" action="{{ route('admin.gallery.update', $item->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Side: Form Fields -->
            <div class="lg:col-span-2 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="type">Type</label>
                        <select name="type" id="type" onchange="toggleTypeFields()" 
                                class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all font-bold">
                            <option value="photo" {{ old('type', $item->type) == 'photo' ? 'selected' : '' }}>Photo</option>
                            <option value="video" {{ old('type', $item->type) == 'video' ? 'selected' : '' }}>Video</option>
                        </select>
                        @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="title">Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $item->title) }}" 
                               class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all font-bold" 
                               placeholder="Enter Item Title" required>
                        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div id="video-field" class="{{ old('type', $item->type) == 'video' ? '' : 'hidden' }} space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="video_url">Video URL (YouTube/Vimeo)</label>
                        <input type="url" name="video_url" id="video_url" value="{{ old('video_url', $item->video_url) }}" 
                               class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all text-sm" 
                               placeholder="https://www.youtube.com/watch?v=...">
                        @error('video_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="p-4 bg-slate-50 rounded-xl border-2 border-dashed border-slate-200">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2" for="video_file">Or Upload Video File</label>
                        @if($item->video_path)
                            <div class="mb-3 p-3 bg-indigo-50 rounded-lg flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-file-video text-indigo-600"></i>
                                    <span class="text-xs font-bold text-indigo-700">Currently: {{ basename($item->video_path) }}</span>
                                </div>
                                <a href="{{ asset('storage/' . $item->video_path) }}" target="_blank" class="text-[10px] font-bold text-indigo-600 hover:underline px-2 py-1 bg-white rounded border border-indigo-200">View</a>
                            </div>
                        @endif
                        <input type="file" name="video_file" id="video_file" accept="video/*"
                               class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="mt-2 text-[10px] text-gray-400 italic">Max size: 20MB. Formats: MP4, MOV, OGG.</p>
                        @error('video_file') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="category">Category</label>
                        <input type="text" name="category" id="category" value="{{ old('category', $item->category) }}" 
                               class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all text-sm" 
                               placeholder="e.g. Campus, Sports, Annual Day">
                        @error('category') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="date">Event Date</label>
                        <input type="date" name="date" id="date" value="{{ old('date', $item->date ? $item->date->format('Y-m-d') : '') }}" 
                               class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all text-sm">
                        @error('date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="description">Description</label>
                    <textarea name="description" id="description" rows="4" 
                              class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all text-sm" 
                              placeholder="Provide some context for the item...">{{ old('description', $item->description) }}</textarea>
                    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Right Side: Photo Preview & Upload -->
            <div id="photo-field" class="lg:col-span-1 border-t lg:border-t-0 lg:border-l border-gray-100 pt-6 lg:pt-0 lg:pl-8 {{ old('type', $item->type) == 'photo' ? '' : 'hidden' }}">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-4">Photo Preview</label>
                <div class="flex flex-col items-center gap-4">
                    <div class="w-full aspect-square border-2 border-dashed border-gray-200 rounded-xl overflow-hidden flex items-center justify-center transition-all bg-indigo-50/10">
                        @if($item->image_path)
                            <img id="preview" src="{{ asset('storage/' . $item->image_path) }}" alt="Preview" class="w-full h-full object-cover">
                        @else
                            <img id="preview" src="#" alt="Preview" class="hidden w-full h-full object-cover">
                            <div id="placeholder" class="text-center p-4">
                                <i class="fas fa-image text-3xl text-gray-300 mb-2"></i>
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">No Image</p>
                            </div>
                        @endif
                    </div>
                    
                    <div class="w-full">
                        <input type="file" name="image" id="image" class="hidden" onchange="previewImage(this)">
                        <button type="button" onclick="document.getElementById('image').click()" 
                                class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-bold shadow-sm">
                            <i class="fas fa-camera mr-2"></i> Choose Photo
                        </button>
                    </div>

                    @error('image') <p class="mt-1 text-xs text-red-600 text-center">{{ $message }}</p> @enderror
                    <p class="text-[10px] text-gray-400 italic text-center">
                        Selected file will replace the current photo upon saving.
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
                if (placeholder) placeholder.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection