@extends('layouts.app')

@section('title', 'Notice Board')

@section('content')
<section class="py-16">
    <div class="mx-auto max-w-[90%] px-4 sm:px-6 lg:px-8">
        <p class="text-sm font-semibold uppercase tracking-[0.35em] text-slate-500">Notices</p>
        <h1 class="mt-4 text-4xl font-black text-slate-950">Notice Board</h1>

        <div class="mt-10 space-y-5 font-bn">
            @forelse ($notices as $notice)
                <a href="{{ route('notices.show', $notice) }}" class="block">
                    <article class="rounded-3xl px-6 py-5 shadow-sm {{ $notice->is_urgent ? 'bg-rose-50' : 'bg-sky-50' }}">
                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-3">
                                    @if ($notice->is_urgent)
                                        <span class="rounded-full bg-rose-600 px-3 py-1 text-xs font-bold uppercase tracking-[0.2em] text-white">Urgent</span>
                                    @endif
                                    <span class="text-sm font-semibold text-slate-500">{{ optional($notice->published_at)->format('d M Y') }}</span>
                                </div>
                                <h2 class="mt-3 text-2xl font-black text-slate-950">{{ $notice->title }}</h2>
                            </div>
                        </div>
                    </article>
                </a>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-slate-500">No notices found.</div>
            @endforelse
        </div>

        @if($notices->hasPages())
        <div class="mt-12 bg-white p-6 rounded-3xl shadow-sm border border-slate-100 italic">
            {{ $notices->links() }}
        </div>
        @endif
    </div>
</section>
@endsection
