@extends('layouts.app')

@section('title', $notice->title)

@section('content')
<section class="py-16">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div>
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

            @php
                $pdfArtifact = $notice->artifacts->first(fn($a) => str_ends_with(strtolower($a->file_path), '.pdf') || strtolower($a->file_type) === 'pdf');
                $imageArtifacts = $notice->artifacts->filter(fn($a) => in_array(strtolower(pathinfo($a->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']));
            @endphp

            @if($imageArtifacts->isNotEmpty())
                <section class="mt-12 space-y-8">
                    @foreach($imageArtifacts as $image)
                        <div class="rounded-3xl overflow-hidden border-4 border-slate-100 shadow-xl">
                            <img src="/storage/{{ ltrim($image->file_path, '/') }}" alt="{{ $image->file_name }}" class="w-full h-auto">
                        </div>
                    @endforeach
                </section>
            @endif

            @if ($notice->artifacts->isNotEmpty())
                <section class="mt-10 border-t border-slate-200 pt-8">
                    <h2 class="text-2xl font-black text-slate-950">Attached Files</h2>
                    <div class="mt-6 space-y-4">
                        @foreach ($notice->artifacts as $artifact)
                            <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-5 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <p class="font-bold text-slate-950">{{ $artifact->file_name }}</p>
                                </div>
                                <a href="/storage/{{ ltrim($artifact->file_path, '/') }}" target="_blank" rel="noreferrer" class="inline-flex rounded-xl border border-slate-950 px-5 py-3 text-sm font-bold text-slate-950 transition hover:bg-slate-950 hover:text-white">Open / Download</a>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($pdfArtifact)
                <section class="mt-12">
                    <div class="overflow-hidden border-4 border-slate-100 shadow-2xl aspect-[1/1.4] md:aspect-[1/1.3] lg:aspect-video w-full">
                        <embed src="/storage/{{ ltrim($pdfArtifact->file_path, '/') }}#toolbar=0&navpanes=0&scrollbar=0" type="application/pdf" width="100%" height="100%">
                    </div>
                </section>
            @endif
        </div>
    </div>
</section>
@endsection
