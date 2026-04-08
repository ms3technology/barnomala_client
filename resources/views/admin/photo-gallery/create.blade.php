@extends('layouts.admin')

@section('title', 'Add Photo')

@push('header_actions')
<button type="submit" form="photo-form" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
    <i class="fas fa-plus mr-2"></i>
    Save Photo
</button>
@endpush

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800">Add New Photo</h1>
    <a href="{{ route('admin.photo-gallery.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:opacity-90 transition-opacity text-sm font-bold">
        <i class="fas fa-arrow-left mr-2"></i> Back
    </a>
</div>

<form id="photo-form" action="{{ route('admin.photo-gallery.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-3 space-y-4">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="title">Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" 
                               class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all font-bold" 
                               placeholder="Enter Photo Title" required>
                        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

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

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="description">Description</label>
                        <textarea name="description" id="description" rows="4" 
                                  class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all text-sm" 
                                  placeholder="Provide some context for the photo...">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-4">
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
                        <input type="file" name="image" id="image" class="absolute inset-0 opacity-0 cursor-pointer" onchange="previewImage(this)" required>
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