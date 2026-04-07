@extends('layouts.admin')

@section('title', 'News Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <p class="text-sm text-slate-500 mt-1">Manage institutional news and articles.</p>
    </div>
    <a href="{{ route('admin.news.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-bold shadow-sm">
        <i class="fas fa-plus mr-2"></i> New News Item
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold">
                <tr>
                    <th class="px-6 py-4 uppercase tracking-wider text-xs">Title & Image</th>
                    <th class="px-6 py-4 uppercase tracking-wider text-xs">Status</th>
                    <th class="px-6 py-4 uppercase tracking-wider text-xs">Published</th>
                    <th class="px-6 py-4 uppercase tracking-wider text-xs text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($news as $item)
                <tr onclick="window.location='{{ route('admin.news.edit', $item) }}'" class="hover:bg-rose-100 transition-colors group cursor-pointer">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-10 h-10 rounded-md object-cover border border-slate-200 shrink-0">
                            <div>
                                <p class="font-bold text-slate-800 line-clamp-1">{{ $item->title }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            @if($item->is_active)
                                <span class="inline-flex text-[10px] font-bold uppercase px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-full">Active</span>
                            @else
                                <span class="inline-flex text-[10px] font-bold uppercase px-2 py-0.5 bg-slate-100 text-slate-600 rounded-full">Draft</span>
                            @endif
                            @if($item->is_featured)
                                <span class="inline-flex text-[10px] font-bold uppercase px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full">
                                    <i class="fas fa-star mr-1"></i> Featured
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-500">
                        {{ $item->published_at ? $item->published_at->format('M d, Y') : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                            <form action="{{ route('admin.news.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Delete this news item?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="event.stopPropagation()" class="text-slate-400 hover:text-red-600 transition-colors" title="Delete">
                                    <i class="fas fa-trash-alt fa-lg"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-newspaper text-slate-300 text-4xl mb-4"></i>
                            <p class="font-medium text-slate-600">No news items found</p>
                            <p class="text-sm mt-1">Get started by creating a new news post.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($news->hasPages())
<div class="mt-6 flex justify-end">
    {{ $news->links() }}
</div>
@endif
@endsection
