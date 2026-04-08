@extends('layouts.admin')

@section('title', 'Photo Gallery Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <p class="text-sm text-slate-500 mt-1">Manage institutional photo gallery items.</p>
    </div>
    <a href="{{ route('admin.photo-gallery.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-bold shadow-sm">
        <i class="fas fa-plus mr-2"></i> New Photo
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold">
                <tr>
                    <th class="px-6 py-4 uppercase tracking-wider text-xs">Title & Image</th>
                    <th class="px-6 py-4 uppercase tracking-wider text-xs">Category</th>
                    <th class="px-6 py-4 uppercase tracking-wider text-xs">Date</th>
                    <th class="px-6 py-4 uppercase tracking-wider text-xs text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($photos as $photo)
                <tr onclick="window.location='{{ route('admin.photo-gallery.edit', $photo) }}'" class="hover:bg-rose-100 transition-colors group cursor-pointer">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('storage/' . $photo->image_path) }}" alt="{{ $photo->title }}" class="w-10 h-10 rounded-md object-cover border border-slate-200 shrink-0">
                            <div>
                                <p class="font-bold text-slate-800 line-clamp-1">{{ $photo->title }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex text-[10px] font-bold uppercase px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full">
                            {{ $photo->category ?? 'General' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs font-medium text-slate-500">
                        {{ $photo->date ? $photo->date->format('M d, Y') : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2" onclick="event.stopPropagation()">
                            <a href="{{ route('admin.photo-gallery.edit', $photo) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors group/edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.photo-gallery.destroy', $photo) }}" method="POST" class="inline" onsubmit="return confirm('Delete this photo?')">
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
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-300">
                                <i class="fas fa-image text-xl"></i>
                            </div>
                            <p class="text-slate-500 font-medium">No photos found</p>
                            <a href="{{ route('admin.photo-gallery.create') }}" class="text-indigo-600 text-sm font-bold hover:underline mt-2">Add your first photo</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($photos->hasPages())
    <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
        {{ $photos->links() }}
    </div>
    @endif
</div>
@endsection