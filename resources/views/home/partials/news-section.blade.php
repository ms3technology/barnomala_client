<section class="py-16 bg-slate-50 relative overflow-hidden reveal">
    <!-- Decors -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-accent/5 rounded-full blur-[100px] -mr-48 -mt-48"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-indigo-500/5 rounded-full blur-[100px] -ml-48 -mb-48"></div>

    <div class="max-w-[90%] mx-auto px-0 md:px-6 lg:px-8 relative z-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div>
                <p class="text-accent font-black uppercase tracking-[0.3em] text-xs mb-4">Inside Campus</p>
                <h2 class="text-2xl md:text-4xl font-black text-slate-900">Latest <span class="text-accent">News</span> & Updates</h2>
                <div class="mt-4 w-24 h-1.5 bg-accent rounded-full"></div>
            </div>
            <a href="{{ route('news.index') }}" class="group flex items-center gap-4 bg-white px-8 py-4 rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 hover:bg-slate-950 hover:text-white transition-all duration-500 font-black uppercase tracking-widest text-xs">
                Browse All News
                <i class="fas fa-arrow-right transform group-hover:translate-x-2 transition-transform"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($featuredNews as $news)
                <article class="group bg-white rounded-2xl overflow-hidden border border-slate-100 shadow-lg hover:shadow-xl transition-all duration-500 h-full flex flex-col">
                    <div class="relative aspect-video overflow-hidden">
                        <img src="{{ $news->image_url }}" alt="{{ $news->title }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                        @if($news->is_featured)
                            <div class="absolute top-4 right-4 px-3 py-1 bg-yellow-400 rounded-full shadow-lg border-2 border-white flex items-center gap-1">
                                <i class="fas fa-star text-yellow-900 text-[10px]"></i>
                                <span class="text-[8px] font-black text-yellow-900 uppercase tracking-widest">Featured</span>
                            </div>
                        @endif
                        <a href="{{ route('news.show', $news) }}" class="absolute inset-0 z-10"></a>
                    </div>
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-accent"></span>
                            {{ $news->published_at ? $news->published_at->format('M d, Y') : '' }}
                        </div>
                        <h3 class="text-lg font-black text-slate-900 leading-tight group-hover:text-accent transition-colors line-clamp-2">
                            <a href="{{ route('news.show', $news) }}">
                                {{ $news->title }}
                            </a>
                        </h3>
                    </div>
                </article>
            @empty
                <div class="col-span-full py-20 text-center bg-slate-100 rounded-4xl border-2 border-dashed border-slate-200">
                    <i class="fas fa-newspaper text-slate-200 text-6xl mb-6"></i>
                    <p class="text-slate-400 font-bold">Stay tuned for upcoming news and stories.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
