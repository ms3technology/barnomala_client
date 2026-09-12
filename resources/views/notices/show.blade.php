@extends('layouts.app')

@section('title', $notice->title)

@section('content')
<section class="py-12">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <div>
            <div class="mt-8 border-b border-slate-200 pb-8">
                <div class="flex flex-wrap items-left gap-3">
                    @if ($notice->is_urgent)
                        <span class="rounded-full bg-rose-600 px-3 py-1 text-xs font-bold uppercase tracking-[0.2em] text-white">Urgent</span>
                    @endif
                    <span class="text-sm font-semibold text-slate-500">Published {{ optional($notice->published_at)->format('d M Y') }}</span>
                </div>
                <h1 class="font-bn mt-4 text-2xl font-black text-slate-950">{{ $notice->title }}</h1>
            </div>

            <article class="font-bn prose mt-8 max-w-none whitespace-pre-line text-slate-700 prose-headings:text-slate-950 prose-a:text-slate-950">
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

            @if($pdfArtifact)
                <section class="mt-12">
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        {{-- Header --}}
                        <div class="flex flex-col gap-4 border-b border-slate-100 p-5 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-red-50">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21h10a2 2 0 002-2V9.414a2 2 0 00-.586-1.414l-5.414-5.414A2 2 0 0011.586 2H7a2 2 0 00-2 2v15a2 2 0 002 2z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 3v6h6"/>
                                    </svg>
                                </div>

                                <div>
                                    <h2 class="text-lg font-semibold text-slate-900">
                                        Official Notice
                                    </h2>
                                    <p class="text-sm text-slate-500">
                                        PDF Document
                                    </p>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex gap-2">
                                <a
                                    href="/storage/{{ ltrim($pdfArtifact->file_path, '/') }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800"
                                >
                                    View Notice
                                </a>

                                <a
                                    href="/storage/{{ ltrim($pdfArtifact->file_path, '/') }}"
                                    download
                                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                                >
                                    Download
                                </a>
                            </div>
                        </div>

                        {{-- Preview --}}
                        <div class="bg-slate-50 py-4">
                            <div class="mx-auto max-w-4xl overflow-hidden rounded-lg border border-slate-200 bg-white shadow-md">
                                <iframe
                                    src="/storage/{{ ltrim($pdfArtifact->file_path, '/') }}"
                                    class="h-[70vh] min-h-[500px] w-full"
                                    title="Official Notice PDF"
                                ></iframe>
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </div>
</section>
@endsection
