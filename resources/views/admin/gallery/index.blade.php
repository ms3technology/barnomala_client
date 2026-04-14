@extends('layouts.admin')

@section('title', 'Gallery Management')

@push('header_actions')
    <a href="{{ route('admin.gallery.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-bold shadow-sm">
        <i class="fas fa-plus mr-2"></i> New Item
    </a>
@endpush

@section('content')
<div class="p-6">
    <div class="overflow-x-auto scrollbar-hide mb-8">
        <div class="flex gap-2 min-w-max">
            <a href="{{ route('admin.gallery.index') }}" 
            class="px-4 py-1.5 rounded-full text-xs font-bold transition-all {{ !request('category') ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-slate-600 border border-slate-200 hover:border-indigo-300' }}">
                All
            </a>
            @foreach($categories as $category)
                <a href="{{ route('admin.gallery.index', ['category' => $category]) }}" 
                class="px-4 py-1.5 rounded-full text-xs font-bold transition-all {{ request('category') === $category ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-slate-600 border border-slate-200 hover:border-indigo-300' }}">
                    {{ $category }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
        @forelse($items as $item)
        <div onclick="window.location='{{ route('admin.gallery.edit', $item) }}'" class="group relative aspect-square bg-slate-100 rounded-xl overflow-hidden cursor-pointer border border-slate-200 hover:shadow-lg transition-all">
            @if($item->type === 'photo' && $item->image_path)
                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            @else
                <div class="w-full h-full flex flex-col items-center justify-center bg-slate-50 text-slate-400">
                    <i class="fas fa-video text-3xl mb-2"></i>
                    <span class="text-[10px] font-bold uppercase tracking-tight">Video</span>
                </div>
            @endif

            {{-- Overlay info --}}
            <div class="absolute inset-0 bg-linear-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
                <h3 class="text-white font-bold text-sm line-clamp-2 leading-tight mb-1">{{ $item->title }}</h3>
                <div class="flex items-center justify-between mt-auto">
                    <span class="text-[10px] text-white/80 font-medium">
                        {{ $item->date ? $item->date->format('M Y') : '' }}
                    </span>
                    <div class="flex gap-2" onclick="event.stopPropagation()">
                        <a href="{{ route('admin.gallery.edit', $item) }}" class="w-8 h-8 flex items-center justify-center bg-white/20 hover:bg-white/40 text-white rounded-lg backdrop-blur-sm transition-colors">
                            <i class="fas fa-edit text-xs"></i>
                        </a>
                        <form action="{{ route('admin.gallery.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Delete this gallery item?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 flex items-center justify-center bg-rose-500/80 hover:bg-rose-600 text-white rounded-lg backdrop-blur-sm transition-colors">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Type badges --}}
            <div class="absolute top-3 left-3 flex gap-2 pointer-events-none">
                @if($item->type === 'photo')
                    <span class="bg-white/90 backdrop-blur-sm text-green-700 text-[9px] font-black uppercase px-2 py-0.5 rounded shadow-sm border border-green-100">
                        <i class="fas fa-camera mr-1"></i> PHOTO
                    </span>
                @else
                    <span class="bg-white/90 backdrop-blur-sm text-purple-700 text-[9px] font-black uppercase px-2 py-0.5 rounded shadow-sm border border-purple-100">
                        <i class="fas fa-play mr-1"></i> VIDEO
                    </span>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full py-24 text-center">
            <div class="flex flex-col items-center gap-4">
                <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center text-slate-200">
                    <i class="fas fa-images text-4xl"></i>
                </div>
                <div>
                    <p class="text-slate-500 font-bold text-lg">Your gallery is empty</p>
                    <p class="text-slate-400 text-sm">Start by adding your first photo or video</p>
                </div>
                <a href="{{ route('admin.gallery.create') }}" class="mt-4 px-6 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 shadow-sm transition-colors">
                    Add Item
                </a>
            </div>
        </div>
        @endforelse
    </div>

    @if($items->hasPages())
    <div class="mt-8">
        {{ $items->links() }}
    </div>
    @endif
</div>
@endsection