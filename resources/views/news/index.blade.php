@extends('layouts.app')

@section('title', 'News & Events')

@section('content')
<section class="py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div>
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-12">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.35em] text-slate-500">Updates</p>
                    <h1 class="mt-4 text-4xl font-black text-slate-950">Campus News & Events</h1>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @forelse ($news as $item)
                    <article class="flex flex-col rounded-3xl overflow-hidden border border-slate-100 bg-slate-50 shadow-sm transition-all duration-300 hover:shadow-xl hover:shadow-slate-200/50 hover:-translate-y-2 group">
                        <a href="{{ route('news.show', $item) }}" class="aspect-video relative overflow-hidden">
                            <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            @if($item->is_featured)
                                <div class="absolute top-4 right-4 h-10 w-10 rounded-full bg-yellow-400 flex items-center justify-center text-yellow-900 shadow-lg border-2 border-white">
                                    <i class="fas fa-star text-xs"></i>
                                </div>
                            @endif
                        </a>
                        <a href="{{ route('news.show', $item) }}" class="p-3 flex-1 flex flex-col">
                            <div class="flex items-center gap-3 text-xs font-bold text-slate-400 mb-4 uppercase tracking-widest">
                                <i class="far fa-calendar-alt"></i>
                                {{ $item->published_at ? $item->published_at->format('d M, Y') : 'N/A' }}
                            </div>
                            <h2 class="font-black text-slate-950 leading-tight mb-4 group-hover:text-accent transition-colors">
                                {{ Str::limit($item->title, 60) }}
                            </h2>
                        </a>
                    </article>
                @empty
                    <div class="col-span-full py-20 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200 text-center">
                        <i class="fas fa-newspaper text-slate-200 text-6xl mb-6"></i>
                        <h3 class="text-2xl font-black text-slate-950 mb-2">No news found</h3>
                        <p class="text-slate-500">Check back later for recent updates and campus events.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-16">
                {{ $news->links() }}
            </div>
        </div>
    </div>
</section>
@endsection
