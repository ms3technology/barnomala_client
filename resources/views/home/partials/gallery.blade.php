<section class="py-12 bg-slate-900 overflow-hidden reveal">
    <style>
        @keyframes scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(calc(-320px * {{ count($galleryItems) }} - 1.5rem * {{ count($galleryItems) }})); }
        }
        .animate-scroll {
            animation: scroll 40s linear infinite;
        }
        .animate-scroll:hover {
            animation-play-state: paused;
        }
    </style>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-12">
            <div class="relative">
                <div class="absolute -left-4 -top-4 w-12 h-12 bg-accent/20 rounded-full blur-xl animate-pulse"></div>
                <p class="text-accent font-black uppercase tracking-[0.3em] text-xs mb-4 relative z-10">Visual Journey</p>
                <h2 class="text-4xl md:text-5xl font-black text-white relative z-10"><span class="text-accent">Gallery</span></h2>
                <div class="mt-4 w-20 h-1.5 bg-accent rounded-full"></div>
            </div>
            <a href="{{ route('gallery.index') }}" class="group flex items-center gap-3 text-white/60 hover:text-white transition-all font-bold uppercase tracking-widest text-xs">
                View All Pictures
                <span class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center group-hover:bg-accent group-hover:border-accent transition-all duration-500">
                    <i class="fas fa-arrow-right text-[10px] transform group-hover:translate-x-1 transition-transform"></i>
                </span>
            </a>
        </div>

        <div class="relative w-full overflow-hidden">
            <div class="flex items-center gap-6 pb-8 animate-scroll whitespace-nowrap">
                @forelse($galleryItems as $item)
                    <div class="group relative min-w-70 md:min-w-[320px] aspect-square overflow-hidden rounded-3xl bg-slate-800 border border-white/5 shadow-2xl transition-all duration-700 hover:-translate-y-2">
                        @if($item->type === 'photo')
                            <img src="{{ asset('storage/' . $item->image_path) }}" 
                                 alt="{{ $item->title }}" 
                                 class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-125 group-hover:rotate-3 opacity-60 group-hover:opacity-100">
                        @elseif($item->type === 'video')
                            @if($item->video_path)
                                <video class="w-full h-full object-cover opacity-60 group-hover:opacity-100 transition-opacity duration-1000" muted loop onmouseover="this.play()" onmouseout="this.pause()">
                                    <source src="{{ asset('storage/' . $item->video_path) }}" type="video/mp4">
                                </video>
                            @else
                                {{-- Placeholder for external video link --}}
                                <div class="w-full h-full bg-slate-900 flex items-center justify-center opacity-60 group-hover:opacity-100 transition-opacity duration-1000">
                                    <i class="fas fa-play-circle text-white/20 text-5xl group-hover:text-accent/40 transition-colors"></i>
                                </div>
                            @endif
                        @endif
                        
                        <div class="absolute top-4 right-4 z-20">
                            @if($item->type === 'video')
                                <span class="w-8 h-8 rounded-full bg-accent/90 text-white flex items-center justify-center shadow-lg">
                                    <i class="fas fa-video text-[10px]"></i>
                                </span>
                            @else
                                <span class="w-8 h-8 rounded-full bg-white/10 backdrop-blur-md text-white/60 flex items-center justify-center border border-white/10 group-hover:bg-accent/90 group-hover:text-white transition-all">
                                    <i class="fas fa-camera text-[10px]"></i>
                                </span>
                            @endif
                        </div>
                        
                        <div class="absolute inset-0 bg-linear-to-t from-slate-950 via-slate-950/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 pointer-events-none"></div>
                        
                        <div class="absolute inset-0 p-6 flex flex-col justify-end translate-y-8 group-hover:translate-y-0 transition-all duration-500 opacity-0 group-hover:opacity-100">
                            <p class="text-[10px] font-black text-accent uppercase tracking-[0.2em] mb-2">{{ $item->date ? $item->date->format('M d, Y') : '' }}</p>
                            <h4 class="text-sm font-bold text-white leading-tight line-clamp-2">{{ $item->title }}</h4>
                            <a href="{{ route('gallery.show', $item->id) }}" class="absolute inset-0 z-10"></a>
                        </div>
                    </div>
                @empty
                    <div class="w-full py-20 text-center bg-white/5 rounded-3xl border-2 border-dashed border-white/10">
                        <i class="fas fa-camera text-white/20 text-6xl mb-6"></i>
                        <p class="text-white/40 font-bold">No items available in the gallery yet.</p>
                    </div>
                @endforelse

                {{-- Duplicate items for seamless loop --}}
                @foreach($galleryItems as $item)
                    <div class="group relative min-w-70 md:min-w-[320px] aspect-square overflow-hidden rounded-3xl bg-slate-800 border border-white/5 shadow-2xl transition-all duration-700 hover:-translate-y-2">
                        @if($item->type === 'photo')
                            <img src="{{ asset('storage/' . $item->image_path) }}" 
                                 alt="{{ $item->title }}" 
                                 class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-125 group-hover:rotate-3 opacity-60 group-hover:opacity-100">
                        @elseif($item->type === 'video')
                            @if($item->video_path)
                                <video class="w-full h-full object-cover opacity-60 group-hover:opacity-100 transition-opacity duration-1000" muted loop onmouseover="this.play()" onmouseout="this.pause()">
                                    <source src="{{ asset('storage/' . $item->video_path) }}" type="video/mp4">
                                </video>
                            @else
                                {{-- Placeholder for external video link --}}
                                <div class="w-full h-full bg-slate-900 flex items-center justify-center opacity-60 group-hover:opacity-100 transition-opacity duration-1000">
                                    <i class="fas fa-play-circle text-white/20 text-5xl group-hover:text-accent/40 transition-colors"></i>
                                </div>
                            @endif
                        @endif
                        
                        <div class="absolute top-4 right-4 z-20">
                            @if($item->type === 'video')
                                <span class="w-8 h-8 rounded-full bg-accent/90 text-white flex items-center justify-center shadow-lg">
                                    <i class="fas fa-video text-[10px]"></i>
                                </span>
                            @else
                                <span class="w-8 h-8 rounded-full bg-white/10 backdrop-blur-md text-white/60 flex items-center justify-center border border-white/10 group-hover:bg-accent/90 group-hover:text-white transition-all">
                                    <i class="fas fa-camera text-[10px]"></i>
                                </span>
                            @endif
                        </div>
                        
                        <div class="absolute inset-0 bg-linear-to-t from-slate-950 via-slate-950/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 pointer-events-none"></div>
                        
                        <div class="absolute inset-0 p-6 flex flex-col justify-end translate-y-8 group-hover:translate-y-0 transition-all duration-500 opacity-0 group-hover:opacity-100">
                            <p class="text-[10px] font-black text-accent uppercase tracking-[0.2em] mb-2">{{ $item->date ? $item->date->format('M d, Y') : '' }}</p>
                            <h4 class="text-sm font-bold text-white leading-tight line-clamp-2">{{ $item->title }}</h4>
                            <a href="{{ route('gallery.show', $item->id) }}" class="absolute inset-0 z-10"></a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
