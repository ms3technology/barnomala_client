<section class="py-10 md:py-12 bg-slate-900 overflow-hidden reveal">
    <style>
        :root {
            --gallery-mobile-width: 260px;
            --gallery-desktop-width: 320px;
            --gallery-gap: 1rem;
        }

        @media (min-width: 768px) {
            :root {
                --gallery-gap: 1.5rem;
            }
        }

        @keyframes scroll {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(calc(
                    (-1 * var(--gallery-mobile-width) * {{ count($galleryItems) }}) -
                    (var(--gallery-gap) * {{ count($galleryItems) }})
                ));
            }
        }

        @media (min-width: 768px) {
            @keyframes scroll {
                0% {
                    transform: translateX(0);
                }

                100% {
                    transform: translateX(calc(
                        (-1 * var(--gallery-desktop-width) * {{ count($galleryItems) }}) -
                        (var(--gallery-gap) * {{ count($galleryItems) }})
                    ));
                }
            }
        }

        .animate-scroll {
            animation: scroll 40s linear infinite;
        }

        .animate-scroll:hover {
            animation-play-state: paused;
        }
    </style>

    <div class="max-w-[90%] mx-auto px-0 md:px-6 lg:px-8">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 md:gap-8 mb-10 md:mb-12">

            <div class="relative">
                <div class="absolute -left-4 -top-4 w-10 h-10 md:w-12 md:h-12 bg-accent/20 rounded-full blur-xl animate-pulse"></div>

                <p class="text-accent font-black uppercase tracking-[0.25em] text-[10px] sm:text-xs mb-3 md:mb-4 relative z-10">
                    Visual Journey
                </p>

                <h2 class="text-3xl sm:text-4xl md:text-5xl font-black text-white relative z-10">
                    <span class="text-accent">Gallery</span>
                </h2>

                <div class="mt-3 md:mt-4 w-16 md:w-20 h-1.5 bg-accent rounded-full"></div>
            </div>

            <a href="{{ route('gallery.index') }}"
               class="group inline-flex items-center gap-3 text-white/60 hover:text-white transition-all font-bold uppercase tracking-widest text-[10px] sm:text-xs">

                View All Pictures

                <span class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center group-hover:bg-accent group-hover:border-accent transition-all duration-500">
                    <i class="fas fa-arrow-right text-[10px] transform group-hover:translate-x-1 transition-transform"></i>
                </span>
            </a>
        </div>

        <!-- Slider -->
        <div class="relative w-full overflow-hidden">

            <div class="flex items-center gap-4 md:gap-6 pb-4 md:pb-8 animate-scroll whitespace-nowrap">

                @foreach([...$galleryItems, ...$galleryItems] as $item)

                    <div class="group relative
                                min-w-65 md:min-w-[320px]
                                h-64 sm:h-72 md:h-80
                                overflow-hidden rounded-2xl md:rounded-3xl
                                bg-slate-800 border border-white/5
                                shadow-2xl transition-all duration-700
                                hover:-translate-y-2">

                        @if($item->type === 'photo')

                            <img src="{{ asset($item->category === 'sample' ? $item->image_path : 'storage/' . $item->image_path) }}"
                                 alt="{{ $item->title }}"
                                 class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110 md:group-hover:scale-125 md:group-hover:rotate-3 opacity-70 group-hover:opacity-100">

                        @elseif($item->type === 'video')

                            @if($item->video_path)

                                <video
                                    class="w-full h-full object-cover opacity-70 group-hover:opacity-100 transition-opacity duration-1000"
                                    muted
                                    loop
                                    playsinline
                                    onmouseover="this.play()"
                                    onmouseout="this.pause()">

                                    <source src="{{ asset($item->category === 'sample' ? $item->video_path : 'storage/' . $item->video_path) }}" type="video/mp4">
                                </video>

                            @else

                                <div class="w-full h-full bg-slate-900 flex items-center justify-center opacity-70 group-hover:opacity-100 transition-opacity duration-1000">
                                    <i class="fas fa-play-circle text-white/20 text-4xl md:text-5xl group-hover:text-accent/40 transition-colors"></i>
                                </div>

                            @endif

                        @endif

                        <!-- Icon -->
                        <div class="absolute top-3 right-3 md:top-4 md:right-4 z-20">

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

                        <!-- Overlay -->
                        <div class="absolute inset-0 bg-linear-to-t from-slate-950 via-slate-950/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 pointer-events-none"></div>

                        <!-- Content -->
                        <div class="absolute inset-0 p-4 md:p-6 flex flex-col justify-end translate-y-6 md:translate-y-8 group-hover:translate-y-0 transition-all duration-500 opacity-0 group-hover:opacity-100">

                            <p class="text-[10px] font-black text-accent uppercase tracking-[0.2em] mb-2">
                                {{ $item->date ? $item->date->format('M d, Y') : '' }}
                            </p>

                            <h4 class="text-sm md:text-base font-bold text-white leading-tight line-clamp-2">
                                {{ $item->title }}
                            </h4>

                            <a href="{{ route('gallery.show', $item->id) }}"
                               class="absolute inset-0 z-10"></a>
                        </div>

                    </div>

                @endforeach

            </div>
        </div>
    </div>
</section>