<section class="py-24 bg-slate-50 relative overflow-hidden">
    <!-- Decors -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-accent/5 rounded-full blur-[100px] -mr-48 -mt-48"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-indigo-500/5 rounded-full blur-[100px] -ml-48 -mb-48"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-16">
            <div>
                <p class="text-accent font-black uppercase tracking-[0.3em] text-xs mb-4">Inside Campus</p>
                <h2 class="text-4xl md:text-5xl font-black text-slate-900">Latest <span class="text-accent">News</span> & Updates</h2>
                <div class="mt-4 w-24 h-1.5 bg-accent rounded-full"></div>
            </div>
            <a href="{{ route('news.index') }}" class="group flex items-center gap-4 bg-white px-8 py-4 rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 hover:bg-slate-950 hover:text-white transition-all duration-500 font-black uppercase tracking-widest text-xs">
                Browse All News
                <i class="fas fa-arrow-right transform group-hover:translate-x-2 transition-transform"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($featuredNews as $news)
                <article class="group bg-white rounded-4xl overflow-hidden border border-slate-100 shadow-2xl shadow-slate-200/40 hover:shadow-accent/10 transition-all duration-500 h-full flex flex-col">
                    <div class="relative aspect-video overflow-hidden">
                        <img src="{{ $news->image_url }}" alt="{{ $news->title }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                        @if($news->is_featured)
                            <div class="absolute top-6 right-6 px-4 py-2 bg-yellow-400 rounded-full shadow-lg border-2 border-white flex items-center gap-2">
                                <i class="fas fa-star text-yellow-900 text-xs text-sh"></i>
                                <span class="text-[10px] font-black text-yellow-900 uppercase tracking-widest">Featured</span>
                            </div>
                        @endif
                    </div>
                    <div class="p-8 flex-1 flex flex-col">
                        <div class="flex items-center gap-3 text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">
                            <span class="w-2 h-2 rounded-full bg-accent"></span>
                            {{ $news->published_at ? $news->published_at->format('M d, Y') : '' }}
                        </div>
                        <h3 class="text-xl font-black text-slate-900 leading-tight mb-4 group-hover:text-accent transition-colors">
                            <a href="{{ route('news.show', $news) }}">
                                {{ Str::limit($news->title, 60) }}
                            </a>
                        </h3>
                        <p class="text-slate-500 text-sm leading-relaxed line-clamp-3 mb-8">
                            {{ $news->summary ?? Str::limit(strip_tags($news->content), 120) }}
                        </p>
                        <div class="mt-auto">
                            <a href="{{ route('news.show', $news) }}" class="inline-flex items-center gap-2 text-[10px] font-black text-accent uppercase tracking-[0.2em] transform group-hover:translate-x-2 transition-all duration-500">
                                Read Story <i class="fas fa-long-arrow-alt-right text-base"></i>
                            </a>
                        </div>
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
