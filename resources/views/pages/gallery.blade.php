@extends('layouts.app')

@section('title', 'Gallery')

@section('content')
<section class="py-16">
    <div class="mx-auto max-w-[90%] px-4 sm:px-6 lg:px-8">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.35em] text-slate-500">Visual Journey</p>
            <h1 class="mt-4 text-4xl font-black text-slate-950">Institutional Gallery</h1>

            <!-- Category Filter Navbar -->
            <div class="mt-10 mb-8 overflow-x-auto">
                <nav class="flex flex-wrap gap-2 md:gap-3 min-w-max pb-4 border-b border-slate-100">
                    <a href="{{ route('gallery.index') }}" 
                       class="px-5 py-2 rounded-full text-xs font-black uppercase tracking-widest transition-all 
                       {{ !request('category') || request('category') === 'All' ? 'bg-accent text-white shadow-lg shadow-accent/20' : 'bg-slate-50 text-slate-500 hover:bg-slate-100' }}">
                        All
                    </a>
                    @foreach($categories as $category)
                    <a href="{{ route('gallery.index', ['category' => $category]) }}" 
                       class="px-5 py-2 rounded-full text-xs font-black uppercase tracking-widest transition-all 
                       {{ request('category') === $category ? 'bg-accent text-white shadow-lg shadow-accent/20' : 'bg-slate-50 text-slate-500 hover:bg-slate-100' }}">
                        {{ $category }}
                    </a>
                    @endforeach
                </nav>
            </div>

            <div class="mt-12">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @forelse($items as $item)
                    <div class="group relative flex flex-col overflow-hidden rounded-2xl bg-slate-50 border border-slate-100 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                        <a href="{{ route('gallery.show', $item->id) }}" class="block aspect-4/3 overflow-hidden relative">
                            @if($item->type === 'photo')
                                <img src="{{ asset( $item->category === 'sample' ? $item->image_path : 'storage/' . $item->image_path) }}" 
                                     alt="{{ $item->title }}" 
                                     class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
                            @elseif($item->type === 'video')
                                @if($item->video_path)
                                    <video class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" muted loop onmouseover="this.play()" onmouseout="this.pause()">
                                        <source src="{{ asset('storage/' . $item->video_path) }}" type="video/mp4">
                                    </video>
                                @else
                                    <div class="w-full h-full bg-slate-200 flex items-center justify-center group-hover:scale-110 transition-transform duration-500">
                                        <i class="fas fa-play-circle text-slate-400 text-5xl"></i>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-100 group-hover:opacity-0 transition-opacity">
                                    <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center border border-white/30">
                                        <i class="fas fa-play text-white text-xs ml-1"></i>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="absolute top-4 right-4 z-10">
                                @if($item->type === 'video')
                                    <span class="w-8 h-8 rounded-full bg-accent/90 text-white flex items-center justify-center shadow-lg border border-accent/20">
                                        <i class="fas fa-video text-[10px]"></i>
                                    </span>
                                @else
                                    <span class="w-8 h-8 rounded-full bg-white/10 backdrop-blur-md text-white/80 flex items-center justify-center border border-white/20 group-hover:bg-accent/90 group-hover:text-white transition-all">
                                        <i class="fas fa-camera text-[10px]"></i>
                                    </span>
                                @endif
                            </div>
                        </a>
                        <div class="flex flex-1 flex-col p-6">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-flex text-[10px] font-bold uppercase px-2 py-0.5 bg-accent/10 text-accent rounded-full">
                                    {{ $item->category ?? 'General' }}
                                </span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                    {{ $item->date ? $item->date->format('M d, Y') : '' }}
                                </span>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 group-hover:text-accent transition-colors line-clamp-2">
                                <a href="{{ route('gallery.show', $item->id) }}">{{ $item->title }}</a>
                            </h3>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-20 text-center bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                        <i class="fas fa-photo-video text-slate-200 text-6xl mb-6"></i>
                        <p class="text-slate-400 font-bold">No items available in the gallery yet.</p>
                    </div>
                    @endforelse
                </div>

                <div class="mt-12">
                    {{ $items->appends(['category' => request('category')])->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection