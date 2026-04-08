@extends('layouts.app')

@section('title', 'Photo Gallery')

@section('content')
<section class="py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.35em] text-slate-500">Visual Journey</p>
            <h1 class="mt-4 text-4xl font-black text-slate-950">Photo Gallery</h1>

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
                    @forelse($photos as $photo)
                    <div class="group relative flex flex-col overflow-hidden rounded-2xl bg-slate-50 border border-slate-100 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                        <a href="{{ route('gallery.show', $photo->id) }}" class="block aspect-4/3 overflow-hidden">
                            <img src="{{ asset('storage/' . $photo->image_path) }}" 
                                 alt="{{ $photo->title }}" 
                                 class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
                        </a>
                        <div class="flex flex-1 flex-col p-6">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-flex text-[10px] font-bold uppercase px-2 py-0.5 bg-accent/10 text-accent rounded-full">
                                    {{ $photo->category ?? 'General' }}
                                </span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                    {{ $photo->date ? $photo->date->format('M d, Y') : '' }}
                                </span>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 group-hover:text-accent transition-colors line-clamp-2">
                                <a href="{{ route('gallery.show', $photo->id) }}">{{ $photo->title }}</a>
                            </h3>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-20 text-center bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                        <i class="fas fa-camera text-slate-200 text-6xl mb-6"></i>
                        <p class="text-slate-400 font-bold">No photos recorded in the gallery yet.</p>
                    </div>
                    @endforelse
                </div>

                <div class="mt-12">
                    {{ $photos->appends(['category' => request('category')])->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection