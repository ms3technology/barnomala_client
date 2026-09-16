@extends('layouts.app')

@section('title', 'Download')

@push('styles')
<style>
    .download-container {
        width: calc(100% - 36px);
        margin: 25px auto;
    }

    .download-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .download-header h1 {
        font-size: 2.25rem;
        font-weight: 900;
        color: #0f172a;
        margin: 0;
    }

    .search-area {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .search-input {
        width: 260px;
        height: 37px;
        border: 1px solid #777;
        padding: 4px 9px;
        font-family: inherit;
        font-size: 17px;
        outline: none;
        border-radius: 3px;
    }

    .search-input:focus {
        border-color: #009b43;
    }

    .search-btn {
        width: 38px;
        height: 37px;
        border: none;
        border-radius: 3px;
        color: #fff;
        cursor: pointer;
        font-size: 19px;
    }

    /* Category Navigation */
    .category-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 25px;
    }

    .category-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 9999px;
        background: #fff;
        font-size: 14px;
        font-weight: 700;
        color: #334155;
        text-decoration: none;
        transition: all 0.2s;
    }

    .category-chip:hover {
        border-color: #00a651;
        color: #008d45;
        background: #f0fdf4;
    }

    .category-chip i {
        color: #00a651;
        font-size: 13px;
    }

    /* Section heading */
    .category-heading {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e2e8f0;
        margin: 35px 0 18px;
        scroll-margin-top: 96px;
    }

    .category-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: #f0fdf4;
        color: #008d45;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .category-icon-box i { font-size: 18px; }

    .category-heading h2 {
        font-size: 1.5rem;
        font-weight: 900;
        color: #0f172a;
        margin: 0;
    }

    .category-heading .count {
        font-size: 12px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin: 0;
    }

    /* Table */
    .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .notice-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .notice-table th,
    .notice-table td {
        border: 1px solid #d1d1d1;
        padding: 5px 6px;
        vertical-align: middle;
        word-wrap: break-word;
    }

    .notice-table th {
        height: 37px;
        background: #fafafa;
        text-align: center;
        font-size: 18px;
        font-weight: 700;
        white-space: nowrap;
    }

    .notice-table td {
        height: 53px;
        font-size: 17px;
    }

    .col-number  { width: 5%; text-align: center; font-weight: 600; }
    .col-title   { width: 35%; }
    .col-filename{ width: 35%; }
    .col-filesize{ width: 10%;  text-align: center; white-space: nowrap; }
    .col-action  { width: 15%;  text-align: center; }

    .size-cell {
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
        letter-spacing: 0.02em;
    }

    .filename-cell {
        font-size: 15px;
        color: #0f172a;
        font-weight: 500;
        word-break: break-all;
    }

    .notice-title {
        line-height: 1.55;
        font-weight: 600;
        color: #0f172a;
        white-space: normal;
        overflow-wrap: anywhere;
        word-break: normal;
        height: auto;
        max-height: none;
    }

    .cover-thumb {
        max-width: 90px;
        max-height: 60px;
        object-fit: cover;
        border: 1px solid #e2e8f0;
        border-radius: 3px;
    }

    /* PDF / File Icon (CSS-drawn like sample) */
    .file-icon {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: #000;
        line-height: 1;
        gap: 2px;
    }

    .file-icon .file-symbol {
        position: relative;
        width: 26px;
        height: 30px;
        border: 2px solid #111;
        border-radius: 1px;
        display: block;
    }

    .file-icon .file-symbol::before {
        content: "";
        position: absolute;
        top: -2px;
        right: -2px;
        width: 10px;
        height: 10px;
        background: white;
        border-left: 2px solid #111;
        border-bottom: 2px solid #111;
    }

    .file-icon .file-symbol::after {
        content: attr(data-ext);
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: Arial, sans-serif;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .empty-cell {
        color: #cbd5e1;
        font-size: 18px;
    }

    .table-footer {
        text-align: right;
        margin-top: 12px;
        font-size: 15px;
        color: #475569;
    }

    /* Empty state */
    .empty-state {
        margin-top: 50px;
        border: 2px dashed #cbd5e1;
        background: #f8fafc;
        border-radius: 16px;
        padding: 60px 20px;
        text-align: center;
        color: #64748b;
    }

    .empty-state i {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 12px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .download-container { width: calc(100% - 20px); margin: 15px auto; }
        .download-header { flex-direction: column; align-items: stretch; }
        .search-input { flex: 1; width: auto; }
        .notice-table { min-width: 880px; }
        .download-header h1 { font-size: 1.75rem; }
    }
</style>
@endpush

@section('content')
<section class="mx-auto max-w-6xl py-8 md:py-10">
    <div class="download-container">

        {{-- Top Bar --}}
        <div class="download-header">
            <h1>Resources</h1>

            <form action="{{ url()->current() }}" method="GET" class="search-area" role="search">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    class="search-input"
                    placeholder="Search downloads..."
                    aria-label="Search downloads"
                >
                <button type="submit" class="search-btn" aria-label="Search">🔍</button>
            </form>
        </div>

        @php
            // Build per-post groups: each entry is one post with the artifacts
            // that should be rendered as separate file-rows under it.
            // The post-level columns (number, title, cover, published) are
            // row-spanned across these artifact rows.
            $q = trim((string) request('q', ''));
            $groups = [];
            $serial = 0;
            $fileTotal = 0;

            if (!$q) {
                foreach ($downloads as $post) {
                    $artifacts = $post->artifacts;
                    if ($artifacts->isEmpty()) {
                        continue;
                    }
                    $serial++;
                    $fileTotal += $artifacts->count();
                    $groups[] = [
                        'post'      => $post,
                        'serial'    => $serial,
                        'artifacts' => $artifacts,
                    ];
                }
            } else {
                $needle = mb_strtolower($q);
                foreach ($downloads as $post) {
                    $titleHit  = mb_stripos($post->title ?? '', $needle) !== false;
                    $contentHit = mb_stripos($post->content ?? '', $needle) !== false;
                    $matched   = $post->artifacts->filter(
                        fn ($a) => mb_stripos($a->file_name ?? '', $needle) !== false
                    )->values();

                    if ($titleHit || $contentHit) {
                        $arts = $matched->isNotEmpty() ? $matched : $post->artifacts;
                    } elseif ($matched->isNotEmpty()) {
                        $arts = $matched;
                    } else {
                        continue;
                    }
                    if ($arts->isEmpty()) {
                        continue;
                    }
                    $serial++;
                    $fileTotal += $arts->count();
                    $groups[] = [
                        'post'      => $post,
                        'serial'    => $serial,
                        'artifacts' => $arts,
                    ];
                }
            }
        @endphp

        @if($fileTotal === 0)
            <div class="empty-state">
                @if($q === '')
                    <i class="fas fa-folder-open"></i>
                    <p style="font-weight:600;color:#334155;margin:6px 0 2px;">No downloads are available right now.</p>
                    <p style="font-size:14px;">Please check back later.</p>
                @else
                    <i class="fas fa-search"></i>
                    <p style="font-weight:600;color:#334155;margin:6px 0 2px;">No results for "{{ $q }}"</p>
                    <p style="font-size:14px;">Try a different keyword.</p>
                @endif
            </div>
        @else
            @php
                $defaultCover = asset('images/default-news.png');
            @endphp
            <div class="table-wrapper">
                <table class="notice-table">
                    <thead>
                        <tr>
                            <th class="col-number">No</th>
                            <th class="col-title">Post Title</th>
                            <th class="col-filename">File Name</th>
                            <th class="col-filesize">Size</th>
                            <th class="col-action">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groups as $group)
                            @php
                                $post         = $group['post'];
                                $artifacts    = $group['artifacts'];
                                $span         = $artifacts->count();
                                $coverUrl     = $post->image_url ?? $defaultCover;
                                $hasCustomCover = ($coverUrl !== $defaultCover);
                            @endphp
                            @foreach($artifacts as $j => $artifact)
                                @php
                                    $fileUrl  = '/storage/' . ltrim($artifact->file_path, '/');
                                    $ext      = strtoupper(pathinfo($artifact->file_name, PATHINFO_EXTENSION));
                                    $sizeKb   = $artifact->file_size / 1024;
                                    $sizeText = $sizeKb >= 1024
                                        ? number_format($sizeKb / 1024, 2) . ' MB'
                                        : number_format($sizeKb, 1) . ' KB';
                                @endphp
                                <tr>
                                    @if($j === 0)
                                        <td class="col-number" rowspan="{{ $span }}">{{ $group['serial'] }}</td>

                                        <td class="notice-title" rowspan="{{ $span }}" style="vertical-align: middle;">
                                            {{ $post->title }}
                                            @if($post->class_label)
                                                <div>
                                                    <span style="display:inline-block;color:#475569;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;">
                                                        {{ $post->class_label }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div class="pt-2 text-xs italic font-normal">Published at {{ optional($post->published_at)->format('d-m-Y') ?? '—' }}</div>
                                        </td>
                                    @endif

                                    <td class="filename-cell">
                                        <span class="text-sm" title="{{ $artifact->file_name }}">{{ $artifact->file_name }}</span>
                                    </td>

                                    <td class="col-filesize">
                                        <span class="size-cell">{{ $sizeText }}</span>
                                    </td>

                                    <td class="col-action">
                                        <div class="w-full justify-center flex gap-4 text-sm text-blue-600">
                                            <a href="{{ $fileUrl }}" target="_blank" rel="noreferrer" class="hover:underline">
                                                View
                                            </a>
                                            <a
                                                download
                                                href="{{ $fileUrl }}" class="hover:underline">
                                                Download
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="table-footer" style="text-align:right;margin-top:15px;font-weight:600;">
                Showing {{ $fileTotal }} {{ \Illuminate\Support\Str::plural('file', $fileTotal) }}
            </div>
        @endif
    </div>
</section>
@endsection
