@extends('layouts.admin')

@section('title', 'Create Notice')

@push('header_actions')
<button type="submit" form="notice-form" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
    <i class="fas fa-plus mr-2"></i>
    Create Notice
</button>
@endpush

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.notices.index') }}" class="text-sm font-medium text-accent hover:underline flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Notices
    </a>
    <h1 class="text-2xl font-bold mt-2 text-gray-800">Create New Notice</h1>
</div>

<div class="bg-white rounded-lg shadow max-w-4xl p-8">
    <form id="notice-form" action="{{ route('admin.notices.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($notice))
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 gap-y-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Notice Title</label>
                <input type="text" name="title" id="title" value="{{ old('title', $notice->title ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-accent focus:border-accent" required>
                @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="published_at" class="block text-sm font-medium text-gray-700 mb-1">Publication Date</label>
                    <input type="date" name="published_at" id="published_at" value="{{ old('published_at', isset($notice) && $notice->published_at ? $notice->published_at->format('Y-m-d') : now()->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-accent focus:border-accent" required>
                    @error('published_at') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                <textarea name="content" id="content" rows="10" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-accent focus:border-accent" required>{{ old('content', $notice->content ?? '') }}</textarea>
                @error('content') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex space-x-6 pt-4">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $notice->is_active ?? true) ? 'checked' : '' }} class="h-4 w-4 text-accent border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700 font-medium">Is Published</label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_urgent" id="is_urgent" value="1" {{ old('is_urgent', $notice->is_urgent ?? false) ? 'checked' : '' }} class="h-4 w-4 text-red-500 border-gray-300 rounded">
                    <label for="is_urgent" class="ml-2 block text-sm text-gray-700 font-medium">Mark as Urgent</label>
                </div>
            </div>

            <div class="border-t pt-6 mt-6">
                <h3 class="text-sm font-bold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-paperclip mr-2 text-accent"></i>
                    Attachments
                </h3>
                <input type="file" name="artifacts[]" id="artifacts" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.png,.zip" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-accent focus:border-accent text-sm">
                <p class="text-xs text-gray-500 mt-1">Allowed: PDF, Word, Excel, PowerPoint, Images, Text, ZIP (Max 50MB total)</p>
            </div>
        </div>
    </form>
</div>
@endsection
