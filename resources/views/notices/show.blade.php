@extends('layouts.app')

@section('title', $notice->title)

@section('content')
<section class="py-0">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div>
            <div class="mt-8 border-b border-slate-200 pb-4">
                <div class="flex flex-wrap items-left gap-3">
                    @if ($notice->is_urgent)
                        <span class="rounded-full bg-rose-600 px-3 py-1 text-xs font-bold uppercase tracking-[0.2em] text-white">Urgent</span>
                    @endif
                    <span class="text-sm font-semibold text-slate-500">Published {{ optional($notice->published_at)->format('d M Y') }}</span>
                </div>
                <h1 class="font-bn mt-2 text-2xl font-black text-slate-950">{{ $notice->title }}</h1>
            </div>

            <article class="font-bn pt-2 pb-4 prose max-w-none text-slate-700 prose-headings:text-slate-950 prose-a:text-slate-950">
                <x-lexical.content-renderer
                    :state="$notice->content_lexical"
                    :fallback="nl2br(e($notice->content))" />
            </article>

            @php 
                $imageArtifacts = $notice->artifacts->filter(fn($a) => in_array(strtolower(pathinfo($a->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']));
                $pdfArtifacts = $notice->artifacts->filter(fn($a) => in_array(strtolower(pathinfo($a->file_path, PATHINFO_EXTENSION)), ['pdf', 'docs', 'docx']));
                $activePdfArtifact = 0;
            @endphp

            @if($imageArtifacts->isNotEmpty())
                <section class="space-y-8">
                    @foreach($imageArtifacts as $image)
                        <div class="w-1/2 overflow-hidden border-4 border-slate-100 shadow-xl">
                            <img src="/storage/{{ ltrim($image->file_path, '/') }}" alt="{{ $image->file_name }}" class="w-full h-auto">
                        </div>
                    @endforeach
                </section>
            @endif

            @if($pdfArtifacts->isNotEmpty())
                <section>
                    @foreach ($pdfArtifacts as $pdfArtifact)
                        @php $pdfUrl = '/storage/' . ltrim($pdfArtifact->file_path, '/'); @endphp
                        <div class="overflow-hidden border border-slate-50 bg-white">
                            {{-- Header --}}
                            <div class="flex flex-col gap-4 border-b border-slate-100 p-2 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50">
                                        <svg class="h-4 w-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 21h10a2 2 0 002-2V9.414a2 2 0 00-.586-1.414l-5.414-5.414A2 2 0 0011.586 2H7a2 2 0 00-2 2v15a2 2 0 002 2z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 3v6h6"/>
                                        </svg>
                                    </div>

                                    <div>
                                        <h2 class="font-semibold text-slate-900 hover:text-indigo-500">
                                            <a 
                                                href="/storage/{{ ltrim($pdfArtifact->file_path, '/') }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                            >
                                                {{ $pdfArtifact->file_name ?? "Notice Document" }}
                                            </a>
                                        </h2>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex gap-4">
                                    <a
                                        href="{{ $pdfUrl }}"
                                        download
                                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-400 bg-white px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50"
                                    >
                                        Download
                                    </a>
                                    <button
                                        type="button"
                                        data-pdf-trigger
                                        data-pdf-index="{{ $loop->index }}"
                                        data-pdf-url="{{ $pdfUrl }}"
                                        aria-expanded="{{ $loop->index === $activePdfArtifact ? 'true' : 'false' }}"
                                        aria-controls="pdf-preview"
                                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-900 px-3 py-2 text-xs font-medium text-white transition hover:bg-slate-800"
                                    >
                                        View Notice
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach                    

                    {{-- Single preview block, swaps content based on activePdfArtifact --}}
                    <div
                        id="pdf-preview"
                        class="{{ $pdfArtifacts->isEmpty() ? 'hidden' : 'bg-slate-50' }}"
                    >
                        <div class="overflow-hidden border border-slate-200 bg-white">
                            <div
                                data-pdf-render
                                data-pdf-active="{{ $activePdfArtifact }}"
                                class="pdf-render w-full"
                            ></div>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    (function () {
        if (typeof pdfjsLib === 'undefined') return;

        pdfjsLib.GlobalWorkerOptions.workerSrc =
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        function renderPdf(container, url) {
            container.innerHTML = '<div class="p-6 text-sm text-slate-500">Loading preview…</div>';

            pdfjsLib.getDocument(url).promise.then(pdf => {
                container.innerHTML = '';
                const scale = 3;

                for (let num = 1; num <= pdf.numPages; num++) {
                    pdf.getPage(num).then(page => {
                        const canvas = document.createElement('canvas');
                        const context = canvas.getContext('2d');
                        const viewport = page.getViewport({ scale });

                        canvas.height = viewport.height;
                        canvas.width = viewport.width;
                        canvas.className = 'w-full h-auto mb-4 shadow-sm';
                        canvas.style.display = 'block';

                        container.appendChild(canvas);

                        page.render({ canvasContext: context, viewport: viewport });
                    });
                }
            }).catch(err => {
                container.innerHTML =
                    '<div class="p-6 text-sm text-rose-600">Unable to display PDF preview. ' +
                    '<a href="' + url + '" download class="underline">Download the file</a> instead.</div>';
                console.error(err);
            });
        }

        const preview = document.getElementById('pdf-preview');
        const renderContainer = preview ? preview.querySelector('[data-pdf-render]') : null;

        document.querySelectorAll('[data-pdf-trigger]').forEach(btn => {
            btn.addEventListener('click', () => {
                if (!preview || !renderContainer) return;

                const index = btn.dataset.pdfIndex;
                const url = btn.dataset.pdfUrl;

                // Mark the clicked trigger as the active one
                document.querySelectorAll('[data-pdf-trigger]').forEach(t => {
                    t.setAttribute('aria-expanded', String(t === btn));
                });

                // Activate (no collapse): always show preview and render the chosen PDF
                preview.classList.remove('hidden');
                renderContainer.dataset.pdfActive = index;
                renderPdf(renderContainer, url);
            });
        });

        // Initial render of the active artifact on first load (if any)
        (function initActive() {
            if (!renderContainer) return;
            const activeIndex = renderContainer.dataset.pdfActive;
            if (activeIndex === '' || activeIndex == null) return;
            const activeTrigger = document.querySelector('[data-pdf-trigger][data-pdf-index="' + activeIndex + '"]');
            if (activeTrigger) {
                renderPdf(renderContainer, activeTrigger.dataset.pdfUrl);
            }
        })();
    })();
</script>
@endpush
