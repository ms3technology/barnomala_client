@extends('layouts.admin')

@section('title', 'News Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800">News Management</h1>
    <a href="{{ route('admin.news.create') }}" class="inline-flex items-center px-4 py-2 bg-accent text-white rounded-lg hover:opacity-90 transition-opacity text-sm font-bold">
        <i class="fas fa-plus mr-2"></i> New News Item
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-black text-slate-600 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-black text-slate-600 uppercase tracking-wider">Published</th>
                    <th class="px-6 py-3 text-left text-xs font-black text-slate-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-black text-slate-600 uppercase tracking-wider">Featured</th>
                    <th class="px-6 py-3 text-right text-xs font-black text-slate-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($news as $item)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-10 h-10 rounded object-cover">
                            <div>
                                <p class="font-bold text-sm text-gray-900 line-clamp-1">{{ $item->title }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $item->published_at ? $item->published_at->format('M d, Y') : 'N/A' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($item->is_active)
                            <span class="inline-flex text-[10px] font-black uppercase tracking-widest px-2.5 py-1 bg-green-100 text-green-700 rounded-full">Active</span>
                        @else
                            <span class="inline-flex text-[10px] font-black uppercase tracking-widest px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full">Draft</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($item->is_featured)
                            <span class="inline-flex text-[10px] font-black uppercase tracking-widest px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-full">
                                <i class="fas fa-star mr-1"></i> Featured
                            </span>
                        @else
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.news.edit', $item) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-bold transition-colors">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.news.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Delete this news item?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-bold transition-colors">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-20 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-newspaper text-gray-200 text-3xl mb-4"></i>
                            <p class="text-gray-500 font-bold">No news items found</p>
                            <p class="text-gray-400 text-sm mt-1">Start by creating your first news post</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-10">
    {{ $news->links() }}
</div>
@endsection
