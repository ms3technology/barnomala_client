@extends('layouts.admin')

@section('title', 'Edit Speech')

@push('header_actions')
<button type="submit" form="speech-form" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
    <i class="fas fa-save mr-2"></i>
    Update Speech
</button>
@endpush

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.speeches.index') }}" class="text-sm font-medium text-accent hover:underline flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Speeches
    </a>
    <h1 class="text-2xl font-bold mt-2 text-gray-800">Edit Speech</h1>
</div>

<div class="bg-white rounded-lg shadow max-w-4xl p-8">
    <form id="speech-form" action="{{ route('admin.speeches.update', $speech) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Speaker Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $speech->name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-accent focus:border-accent" required>
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $speech->title) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-accent focus:border-accent" required>
                    @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="designation" class="block text-sm font-medium text-gray-700 mb-1">Designation</label>
                <input type="text" name="designation" id="designation" value="{{ old('designation', $speech->designation) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-accent focus:border-accent" required>
                @error('designation') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="speech" class="block text-sm font-medium text-gray-700 mb-1">Speech Content</label>
                <textarea name="speech" id="speech" rows="10" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-accent focus:border-accent" required>{{ old('speech', $speech->speech) }}</textarea>
                @error('speech') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $speech->is_active) ? 'checked' : '' }} class="h-4 w-4 text-accent border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-700 font-medium">Active</label>
            </div>

            <div class="border-t pt-6 mt-2">
                <h3 class="text-sm font-semibold text-gray-800 mb-2">Current Speaker Image</h3>
                <div class="flex items-center">
                    <img src="{{ $speech->image_url }}" alt="{{ $speech->name }}" class="h-16 w-16 rounded-full object-cover border border-gray-200">
                    <p class="ml-3 text-xs text-gray-500">Image is managed from synced source.</p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection