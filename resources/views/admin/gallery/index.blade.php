@extends('layouts.admin')

@section('title', 'Gallery Management')

@push('header_actions')
    <a href="{{ route('admin.gallery.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-bold shadow-sm">
        <i class="fas fa-plus mr-2"></i> New Item
    </a>
@endpush

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold">
                <tr>
                    <th class="px-6 py-4 uppercase tracking-wider text-xs">Type</th>
                    <th class="px-6 py-4 uppercase tracking-wider text-xs">Title & Content</th>
                    <th class="px-6 py-4 uppercase tracking-wider text-xs">Category</th>
                    <th class="px-6 py-4 uppercase tracking-wider text-xs">Date</th>
                    <th class="px-6 py-4 uppercase tracking-wider text-xs text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($items as $item)
                <tr onclick="window.location='{{ route('admin.gallery.edit', $item) }}'" class="hover:bg-rose-100 transition-colors group cursor-pointer">
                    <td class="px-6 py-4">
                        @if($item->type === 'photo')
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase px-2 py-0.5 bg-green-100 text-green-700 rounded-full">
                                <i class="fas fa-camera"></i> Photo
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full">
                                <i class="fas fa-video"></i> Video
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($item->type === 'photo' && $item->image_path)
                                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}" class="w-10 h-10 rounded-md object-cover border border-slate-200 shrink-0">
                            @else
                                <div class="w-10 h-10 rounded-md bg-slate-100 flex items-center justify-center border border-slate-200 shrink-0">
                                    <i class="fas fa-play text-slate-400"></i>
                                </div>
                            @endif
                            <div>
                                <p class="font-bold text-slate-800 line-clamp-1">{{ $item->title }}</p>
                                @if($item->type === 'video')
                                    <p class="text-[10px] text-slate-400 truncate max-w-xs">{{ $item->video_url }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex text-[10px] font-bold uppercase px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full">
                            {{ $item->category ?? 'General' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs font-medium text-slate-500">
                        {{ $item->date ? $item->date->format('M d, Y') : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2" onclick="event.stopPropagation()">
                            <a href="{{ route('admin.gallery.edit', $item) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors group/edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.gallery.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Delete this gallery item?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-300">
                                <i class="fas fa-images text-xl"></i>
                            </div>
                            <p class="text-slate-500 font-medium">No items found</p>
                            <a href="{{ route('admin.gallery.create') }}" class="text-indigo-600 text-sm font-bold hover:underline mt-2">Add your first item</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($items->hasPages())
    <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
        {{ $items->links() }}
    </div>
    @endif
</div>
@endsection