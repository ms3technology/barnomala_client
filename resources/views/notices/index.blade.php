@extends('layouts.app')

@section('title', 'Notice Board')

@section('content')
<section class="py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-4xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm font-semibold uppercase tracking-[0.35em] text-slate-500">Notices</p>
            <h1 class="mt-4 text-4xl font-black text-slate-950">Notice Board</h1>

            <div class="mt-10 space-y-5">
                @forelse ($notices as $notice)
                    <article class="rounded-3xl border px-6 py-5 shadow-sm {{ $notice->is_urgent ? 'border-rose-200 bg-rose-50' : 'border-sky-200 bg-sky-50' }}">
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
                            <a href="{{ route('notices.show', $notice) }}" class="inline-flex rounded-xl bg-slate-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800">View Details</a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-slate-500">No notices found.</div>
                @endforelse
            </div>

            <div class="mt-10">
                {{ $notices->links() }}
            </div>
        </div>
    </div>
</section>
@endsection
