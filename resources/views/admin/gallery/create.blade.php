@extends('layouts.admin')

@section('title', 'Add Gallery Items')

@push('header_actions')
<div class="flex items-center gap-5">
    <a href="{{ route('admin.gallery.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-sm font-medium text-decoration-none">
        <i class="fas fa-arrow-left mr-2"></i> Back
    </a>
    <button type="submit" form="gallery-form" id="submit-btn" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
        <i class="fas fa-upload mr-2"></i>
        Upload & Save All
    </button>
</div>
@endpush

@section('content')
<form id="gallery-form" action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <!-- Bulk Settings Card -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
        <div class="border-b border-gray-100 pb-4 mb-4">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-cog text-indigo-500"></i>
                Apply to All Settings
            </h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="bulk_type">Type</label>
                <select name="type" id="bulk_type" onchange="toggleBulkFields()" 
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all font-bold text-sm bg-gray-50">
                    <option value="photo" {{ old('type') == 'photo' ? 'selected' : '' }}>Photo</option>
                    <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Video</option>
                </select>
                @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="bulk_category">Category</label>
                <div class="relative" id="category-autocomplete">
                    <input type="text" name="category" id="bulk_category" value="{{ old('category') }}" 
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all text-sm bg-gray-50" 
                           placeholder="e.g. Campus, Sports"
                           autocomplete="off">
                    
                    <!-- Dropdown -->
                    <div id="category-dropdown" 
                         class="hidden absolute z-10 mt-1 w-full max-h-60 overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                        <ul id="category-list" class="divide-y divide-gray-100">
                            @foreach($categories as $category)
                                <li class="category-option text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9 hover:bg-indigo-600 hover:text-white"
                                    data-value="{{ $category }}">
                                    <span class="font-normal block truncate">{{ $category }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @error('category') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="bulk_date">Event Date</label>
                <input type="date" name="date" id="bulk_date" value="{{ old('date', date('Y-m-d')) }}" 
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all text-sm bg-gray-50">
                @error('date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <div id="video-upload-zone" class="hidden space-y-4 m-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="title">Video Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" 
                           class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all font-bold text-sm bg-gray-50" 
                           placeholder="Enter Video Title">
                    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="video_url">Video URL (YouTube/Vimeo)</label>
                        <input type="url" name="video_url" id="video_url" value="{{ old('video_url') }}" 
                               class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all text-sm bg-gray-50" 
                               placeholder="https://www.youtube.com/watch?v=...">
                        @error('video_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl border-2 border-dashed border-slate-200 font-sans">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2" for="video_file">Or Upload Video File</label>
                        <input type="file" name="video_file" id="video_file" accept="video/*"
                               class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="mt-2 text-[10px] text-gray-400 italic font-medium">Max 20MB. MP4, MOV, OGG.</p>
                        @error('video_file') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Container -->
    <div id="preview-grid" class="grid grid-cols-1 sm:grid-cols-4 lg:grid-cols-6 gap-6 p-6">
        <div id="photo-upload-zone" class="col-span-2">
            <label for="photos" class="relative group cursor-pointer block border-4 border-dashed border-gray-200 rounded-3xl transition-all hover:border-indigo-400 hover:bg-indigo-50/30 bg-white">
                <input type="file" id="photos" class="hidden" multiple accept="image/*" onchange="handleFiles(this.files)">
                <div id="hidden-inputs-container"></div>
                <div class="text-center p-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-500 group-hover:scale-110 transition-transform">
                        <i class="fas fa-images text-2xl"></i>
                    </div>
                    <h4 class="font-bold text-gray-800 mb-1">Click or Drag Multiple Photos</h4>
                    <p class="text-xs text-gray-500">Supports JPG, PNG, WEBP</p>
                </div>
            </label>
        </div>

        <!-- Dynamically populated -->
    </div>
</form>

<script>
    const previewGrid = document.getElementById('preview-grid');
    const photoZone = document.getElementById('photo-upload-zone');
    const videoZone = document.getElementById('video-upload-zone');
    const bulkType = document.getElementById('bulk_type');
    const categoryInput = document.getElementById('bulk_category');
    const categoryDropdown = document.getElementById('category-dropdown');
    const categoryList = document.getElementById('category-list');
    const categoryOptions = Array.from(document.querySelectorAll('.category-option'));
    const hiddenInputsContainer = document.getElementById('hidden-inputs-container');
    let uploadedFiles = [];

    function toggleBulkFields() {
        if (bulkType.value === 'photo') {
            photoZone.classList.remove('hidden');
            videoZone.classList.add('hidden');
            previewGrid.classList.remove('hidden');
        } else {
            photoZone.classList.add('hidden');
            videoZone.classList.remove('hidden');
            previewGrid.classList.add('hidden');
            previewGrid.innerHTML = '';
        }
    }

    // Category Autocomplete Logic
    categoryInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        let visibleCount = 0;

        categoryOptions.forEach(option => {
            const value = option.dataset.value.toLowerCase();
            if (value.includes(query)) {
                option.style.display = 'block';
                visibleCount++;
            } else {
                option.style.display = 'none';
            }
        });

        if (visibleCount > 0 && query !== '') {
            categoryDropdown.classList.remove('hidden');
        } else {
            categoryDropdown.classList.add('hidden');
        }
    });

    categoryInput.addEventListener('focus', function() {
        if (this.value !== '') {
            categoryDropdown.classList.remove('hidden');
        } else if (categoryOptions.length > 0) {
            categoryOptions.forEach(option => option.style.display = 'block');
            categoryDropdown.classList.remove('hidden');
        }
    });

    categoryOptions.forEach(option => {
        option.addEventListener('mousedown', function(e) {
            categoryInput.value = this.dataset.value;
            categoryDropdown.classList.add('hidden');
        });
    });

    document.addEventListener('mousedown', function(e) {
        if (!document.getElementById('category-autocomplete').contains(e.target)) {
            categoryDropdown.classList.add('hidden');
        }
    });

    function handleFiles(files) {
        Array.from(files).forEach((file) => {
            if (!file.type.startsWith('image/')) return;
            
            const fileId = Date.now() + Math.random().toString(36).substr(2, 9);
            uploadedFiles.push({ id: fileId, file: file });

            const reader = new FileReader();
            reader.onload = (e) => {
                const previewItem = document.createElement('div');
                previewItem.id = `preview-${fileId}`;
                previewItem.className = 'p-4 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden group animate-in fade-in zoom-in duration-300 transform transition-all hover:scale-[1.02]';
                previewItem.innerHTML = `
                    <div class="aspect-square relative overflow-hidden bg-gray-100">
                        <img src="${e.target.result}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                             <button type="button" onclick="removeFile('${fileId}')" class="bg-red-500 text-white w-8 h-8 rounded-full flex items-center justify-center hover:bg-red-600 transition-colors">
                                <i class="fas fa-trash-alt text-xs"></i>
                             </button>
                        </div>
                    </div>
                    <div class="p-3 bg-white">
                        <input type="text" name="titles[]" required value="" placeholder="Enter title..."
                               class="w-full px-3 py-2 text-xs bg-gray-200 border border-gray-100 rounded-lg focus:border-indigo-400 focus:bg-white transition-all font-bold">
                    </div>
                `;
                previewGrid.appendChild(previewItem);
                updateHiddenInputs();
            };
            reader.readAsDataURL(file);
        });
        document.getElementById('photos').value = ''; // Reset file input to allow re-uploading same file
    }

    function removeFile(fileId) {
        uploadedFiles = uploadedFiles.filter(f => f.id !== fileId);
        document.getElementById(`preview-${fileId}`).remove();
        updateHiddenInputs();
    }

    function updateHiddenInputs() {
        hiddenInputsContainer.innerHTML = '';
        const dataTransfer = new DataTransfer();
        
        uploadedFiles.forEach(f => {
            dataTransfer.items.add(f.file);
        });

        const newFileInput = document.createElement('input');
        newFileInput.type = 'file';
        newFileInput.name = 'images[]';
        newFileInput.multiple = true;
        newFileInput.classList.add('hidden');
        newFileInput.files = dataTransfer.files;
        hiddenInputsContainer.appendChild(newFileInput);
    }

    // Initialize
    toggleBulkFields();
</script>
@endsection
