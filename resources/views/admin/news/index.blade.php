@extends('layouts.admin')

@section('title', 'News Management')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800">News Management</h1>
    <a href="{{ route('admin.news.create') }}" class="inline-flex items-center px-4 py-2 bg-accent text-white rounded-lg hover:opacity-90 transition-opacity text-sm font-bold">
        <i class="fas fa-plus mr-2"></i> New News Item
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($news as $item)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
        <div class="aspect-video bg-gray-100 relative group">
            <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
            @if($item->is_featured)
                <span class="absolute top-2 right-2 bg-yellow-400 text-yellow-900 text-[10px] font-black uppercase px-2 py-1 rounded-md shadow-sm">
                    Featured
                </span>
            @endif
        </div>
        <div class="p-5">
            <div class="flex items-center gap-2 mb-3 text-[10px] text-gray-400 font-bold uppercase tracking-wider">
                <i class="fas fa-calendar"></i>
                {{ $item->published_at ? $item->published_at->format('M d, Y') : 'N/A' }}
                @if($item->artifacts->count())
                    <span class="mx-1">•</span>
                    <i class="fas fa-paperclip"></i>
                    {{ $item->artifacts->count() }} Files
                @endif
            </div>
            
            <h3 class="font-bold text-lg text-gray-900 line-clamp-2 mb-3 leading-snug">{{ $item->title }}</h3>
            
            <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                <div class="flex items-center gap-2">
                    @if($item->is_active)
                        <span class="inline-flex text-[10px] font-black uppercase tracking-widest px-2.5 py-1 bg-green-100 text-green-700 rounded-full">Active</span>
                    @else
                        <span class="inline-flex text-[10px] font-black uppercase tracking-widest px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full">Draft</span>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.news.edit', $item) }}" class="text-indigo-600 hover:text-indigo-900 p-1 transition-colors">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.news.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Delete this news item?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 p-1 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-20 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
        <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
            <i class="fas fa-newspaper text-gray-200 text-3xl"></i>
        </div>
        <p class="text-gray-500 font-bold text-lg">No news items found</p>
        <p class="text-gray-400 text-sm mt-2">Start by creating your first news post</p>
    </div>
    @endforelse
</div>

<div class="mt-10">
    {{ $news->links() }}
</div>
@endsection
