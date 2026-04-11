@extends('layouts.app')

@section('title', 'Committees')

@section('content')
<section class="py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.35em] text-slate-500">Administration</p>
            <h1 class="mt-4 text-4xl font-black text-slate-950">Our Committees</h1>

            <div class="mt-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($committees as $committee)
                <div class="group relative flex flex-col bg-white border border-slate-200 rounded-3xl p-8 transition-all duration-300 hover:shadow-2xl hover:border-accent/20">
                    <div class="flex-1">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-accent/10 text-accent mb-4">
                            {{ $committee->type }}
                        </span>
                        <h3 class="text-2xl font-black text-slate-900 mb-2 truncate group-hover:text-accent transition-colors">
                            <a href="{{ route('committees.show', $committee->id) }}">{{ $committee->name }}</a>
                        </h3>
                        <p class="text-sm font-bold text-slate-500 mb-4">{{ $committee->session }}</p>
                        @if($committee->description)
                            <p class="text-sm text-slate-600 line-clamp-3 mb-6 leading-relaxed">{{ $committee->description }}</p>
                        @endif
                    </div>
                    <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                        <a href="{{ route('committees.show', $committee->id) }}" class="text-xs font-black uppercase tracking-widest text-slate-900 group-hover:text-accent flex items-center gap-2">
                            View Details
                            <i class="fas fa-arrow-right text-[10px] transform group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-20 text-center bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                    <i class="fas fa-users-cog text-slate-200 text-6xl mb-6"></i>
                    <p class="text-slate-400 font-bold">No committees recorded yet.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection
