@extends('layouts.app')

@section('title', $notice->title)

@section('content')
<section class="py-16">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200 md:p-10">
            <a href="{{ route('notices.index') }}" class="text-sm font-bold text-slate-600 transition hover:text-slate-950">&larr; Back to Notice Board</a>

            <div class="mt-8 border-b border-slate-200 pb-8">
                <div class="flex flex-wrap items-center gap-3">
                    @if ($notice->is_urgent)
                        <span class="rounded-full bg-rose-600 px-3 py-1 text-xs font-bold uppercase tracking-[0.2em] text-white">Urgent</span>
                    @endif
                    <span class="text-sm font-semibold text-slate-500">Published {{ optional($notice->published_at)->format('d M Y') }}</span>
                </div>
                <h1 class="mt-4 text-4xl font-black text-slate-950">{{ $notice->title }}</h1>
            </div>

            <article class="prose mt-8 max-w-none whitespace-pre-line text-slate-700 prose-headings:text-slate-950 prose-a:text-slate-950">
                {{ $notice->content }}
            </article>

            @if ($notice->artifacts->isNotEmpty())
                <section class="mt-10 border-t border-slate-200 pt-8">
                    <h2 class="text-2xl font-black text-slate-950">Attached Files</h2>
                    <div class="mt-6 space-y-4">
                        @foreach ($notice->artifacts as $artifact)
                            <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-5 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <p class="font-bold text-slate-950">{{ $artifact->file_name }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ strtoupper($artifact->file_type) }} • {{ number_format(($artifact->file_size ?? 0) / 1024, 2) }} KB</p>
                                </div>
                                <a href="/{{ ltrim($artifact->file_path, '/') }}" target="_blank" rel="noreferrer" class="inline-flex rounded-xl border border-slate-950 px-5 py-3 text-sm font-bold text-slate-950 transition hover:bg-slate-950 hover:text-white">Download</a>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</section>
@endsection
