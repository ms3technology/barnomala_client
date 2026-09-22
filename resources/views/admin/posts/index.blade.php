@extends('layouts.admin')

@section('title', 'Posts')

@push('header_actions')
<a href="{{ route('admin.posts.create') }}"
    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold
        shadow-sm hover:bg-slate-800 hover:-translate-y-0.5 transition-all duration-200"
>
    <i class="fas fa-plus text-xs"></i>
    New Post 
</a>
@endpush

@push('styles')

<style>
    @keyframes postRowIn {
        from {
            opacity: 0;
            transform: translateY(5px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .post-item {
        animation: postRowIn .3s ease-out both;
    }

    .post-item {
        transition:
            background-color .2s ease,
            box-shadow .2s ease,
            transform .2s ease;
    }

    .post-item:hover {
        background: #fafbff;
        box-shadow: inset 3px 0 0 #6366f1;
    }

    .post-item:hover .post-thumb {
        transform: scale(1.04);
    }

    .post-item:hover .post-title {
        color: #4f46e5;
    }

    .post-item:hover .post-action {
        opacity: 1;
        transform: translateX(0);
    }

    .post-thumb {
        transition: transform .25s ease;
    }

    .post-title {
        transition: color .2s ease;
    }

    .post-action {
        opacity: .45;
        transform: translateX(3px);
        transition: opacity .2s ease, transform .2s ease;
    }

    .filter-tab {
        transition: all .2s ease;
    }

    .filter-tab:hover {
        transform: translateY(-1px);
    }

    @media (max-width: 640px) {
        .post-table {
            display: none;
        }
    }

    @media (min-width: 641px) {
        .post-mobile-list {
            display: none;
        }
    }
</style>
@endpush

@section('content')

@php
$typeStyles = [
    'notice' => [
        'bg' => 'bg-amber-50',
        'text' => 'text-amber-700',
        'ring' => 'ring-amber-200/70',
        'dot' => 'bg-amber-500',
    ],
    'news' => [
        'bg' => 'bg-sky-50',
        'text' => 'text-sky-700',
        'ring' => 'ring-sky-200/70',
        'dot' => 'bg-sky-500',
    ],
    'document' => [
        'bg' => 'bg-indigo-50',
        'text' => 'text-indigo-700',
        'ring' => 'ring-indigo-200/70',
        'dot' => 'bg-indigo-500',
    ],
    'admission_form' => [
        'bg' => 'bg-rose-50',
        'text' => 'text-rose-700',
        'ring' => 'ring-rose-200/70',
        'dot' => 'bg-rose-500',
    ],
    'other_forms' => [
        'bg' => 'bg-pink-50',
        'text' => 'text-pink-700',
        'ring' => 'ring-pink-200/70',
        'dot' => 'bg-pink-500',
    ],
    'class_routine' => [
        'bg' => 'bg-teal-50',
        'text' => 'text-teal-700',
        'ring' => 'ring-teal-200/70',
        'dot' => 'bg-teal-500',
    ],
    'exam_routine' => [
        'bg' => 'bg-cyan-50',
        'text' => 'text-cyan-700',
        'ring' => 'ring-cyan-200/70',
        'dot' => 'bg-cyan-500',
    ],
    'syllabus' => [
        'bg' => 'bg-emerald-50',
        'text' => 'text-emerald-700',
        'ring' => 'ring-emerald-200/70',
        'dot' => 'bg-emerald-500',
    ],
    'magazine' => [
        'bg' => 'bg-fuchsia-50',
        'text' => 'text-fuchsia-700',
        'ring' => 'ring-fuchsia-200/70',
        'dot' => 'bg-fuchsia-500',
    ],
    'board_result' => [
        'bg' => 'bg-violet-50',
        'text' => 'text-violet-700',
        'ring' => 'ring-violet-200/70',
        'dot' => 'bg-violet-500',
    ],
];
@endphp

<div class="space-y-6">
    {{-- Category Navigation --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-wrap gap-1.5 p-3">
            <a href="{{ route('admin.posts.index') }}"
            class="filter-tab shrink-0 inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-semibold
            {{ $tab === 'all'
                    ? 'bg-slate-900 text-white shadow-sm'
                    : 'text-slate-600 hover:bg-slate-100' }}">
                <i class="fas fa-layer-group text-xs"></i>
                All
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-bold
                    {{ $tab === 'all'
                        ? 'bg-white/15 text-white'
                        : 'bg-slate-100 text-slate-500' }}">
                    {{ $posts->total() }}
                </span>
            </a>

            @foreach($postTypes as $key => $label)
                <a href="{{ route('admin.posts.index', ['tab' => $key]) }}"
                class="filter-tab shrink-0 inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-semibold
                {{ $tab === $key
                        ? 'bg-slate-900 text-white shadow-sm'
                        : 'text-slate-600 hover:bg-slate-100' }}">

                    <i class="fas {{ \App\Models\Post::typeIcon($key) }} text-xs"></i>
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>


    {{-- Desktop Posts Table --}}
    <div class="post-table bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        {{-- Table Header --}}
        <div class="flex items-center justify-between px-6 py-2 border-b border-slate-100">
            <div>
                <h2 class="text-sm font-bold text-slate-900">
                    {{ $tab === 'all' ? 'All Posts' : ($postTypes[$tab] ?? 'Posts') }}
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Showing {{ $posts->count() }} of {{ $posts->total() }}
                </p>
            </div>
            <div class="hidden sm:flex items-center gap-2 text-xs text-slate-400">
                <i class="fas fa-info-circle"></i>
                Click a row to edit
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100">
                        <th class="px-6 py-2 text-[10px] uppercase tracking-[0.12em] font-bold text-slate-400">
                            Post
                        </th>

                        <th class="px-5 py-2 text-[10px] uppercase tracking-[0.12em] font-bold text-slate-400">
                            Type
                        </th>

                        <th class="px-5 py-2 text-[10px] uppercase tracking-[0.12em] font-bold text-slate-400">
                            Published
                        </th>

                        <th class="px-5 py-2 text-[10px] uppercase tracking-[0.12em] font-bold text-slate-400">
                            Status
                        </th>

                        <th class="px-6 py-2 text-right text-[10px] uppercase tracking-[0.12em] font-bold text-slate-400">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($posts as $post)
                        @php
                            $style = $typeStyles[$post->type] ?? [
                                'bg' => 'bg-slate-50',
                                'text' => 'text-slate-700',
                                'ring' => 'ring-slate-200',
                                'dot' => 'bg-slate-400',
                            ];

                            $typeName = \App\Models\Post::typeLabel($post->type);
                            $imageUrl = $post->image_json['url'] ?? null;
                        @endphp

                        <tr class="post-item group cursor-pointer"
                            onclick="window.location='{{ route('admin.posts.edit', $post) }}'"
                            style="animation-delay: {{ $loop->index * 30 }}ms">
                            {{-- Post --}}
                            <td class="px-4">
                                <div class="flex items-center gap-4 min-w-70">

                                    <div class="post-thumb relative w-10 h-10 rounded-xl overflow-hidden shrink-0
                                                bg-slate-50 ring-1 ring-slate-200 flex items-center justify-center">
                                        @if($imageUrl)
                                            <img src="{{ $imageUrl }}"
                                                alt=""
                                                class="w-full h-full object-cover">
                                        @else
                                            <i class="fas {{ \App\Models\Post::typeIcon($post->type) }}
                                            text-indigo-400 text-sm"></i>
                                        @endif

                                        @if($post->is_urgent)
                                            <span class="absolute top-1 right-1 w-2.5 h-2.5 rounded-full
                                                        bg-rose-500 ring-2 ring-white"
                                                title="Urgent"></span>
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="post-title font-semibold text-slate-800 truncate max-w-85">
                                                {{ $post->title }}
                                            </p>
                                            <i class="fas fa-arrow-right post-action text-[10px] text-indigo-500"></i>
                                        </div>

                                        <div class="flex items-center flex-wrap gap-x-2 gap-y-1 mt-1 text-xs text-slate-400">
                                            @if($post->is_urgent)
                                                <span class="inline-flex items-center gap-1 text-rose-500 font-medium">
                                                    <i class="fas fa-bolt text-[9px]"></i>
                                                    Urgent
                                                </span>
                                                <span class="text-slate-200">•</span>
                                            @endif

                                            @if($post->type === 'document' && $post->class_label)
                                                <span class="inline-flex items-center gap-1">
                                                    <i class="fas fa-graduation-cap text-[9px]"></i>
                                                    {{ $post->class_label }}
                                                </span>
                                            @endif

                                            @if($post->artifacts->count())
                                                @if($post->type === 'document' && $post->class_label)
                                                    <span class="text-slate-200">•</span>
                                                @endif
                                                <span class="inline-flex items-center gap-1">
                                                    <i class="fas fa-paperclip text-[9px]"></i>
                                                    {{ $post->artifacts->count() }}
                                                    {{ Str::plural('file', $post->artifacts->count()) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>


                            {{-- Type --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg
                                            text-xs font-semibold ring-1 ring-inset
                                            {{ $style['bg'] }}
                                            {{ $style['text'] }}
                                            {{ $style['ring'] }}">

                                    <span class="w-1.5 h-1.5 rounded-full {{ $style['dot'] }}"></span>
                                    {{ $typeName }}
                                </span>
                            </td>

                            {{-- Published --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($post->published_at)
                                    <div class="text-sm font-medium text-slate-700">
                                        {{ $post->published_at->format('M d, Y') }}
                                    </div>

                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $post->published_at->diffForHumans() }}
                                    </div>
                                @else
                                    <div class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                        <span class="text-sm text-slate-400">Not published</span>
                                    </div>
                                @endif
                            </td>


                            {{-- Status --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($post->is_active)
                                    <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-full
                                                bg-emerald-50 text-emerald-700
                                                ring-1 ring-inset ring-emerald-200/70
                                                text-[11px] font-bold">
                                        <span class="relative flex w-1.5 h-1.5">
                                            <span class="absolute inline-flex w-full h-full rounded-full
                                                        bg-emerald-400 opacity-50 animate-ping"></span>
                                            <span class="relative inline-flex w-1.5 h-1.5 rounded-full
                                                        bg-emerald-500"></span>
                                        </span>
                                        Live
                                    </span>
                                @else

                                    <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-full
                                                bg-slate-100 text-slate-600
                                                ring-1 ring-inset ring-slate-200
                                                text-[11px] font-bold">

                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Draft
                                    </span>
                                @endif
                            </td>


                            {{-- Action --}}
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('admin.posts.edit', $post) }}"
                                    onclick="event.stopPropagation()"
                                    class="w-9 h-9 inline-flex items-center justify-center rounded-lg
                                            text-slate-400 hover:text-indigo-600 hover:bg-indigo-50
                                            transition-colors"
                                    title="Edit">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>

                                    <form action="{{ route('admin.posts.destroy', $post) }}"
                                        method="POST"
                                        class="inline"
                                        onclick="event.stopPropagation()"
                                        onsubmit="event.stopPropagation(); return confirm('Delete this post?')">

                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-9 h-9 inline-flex items-center justify-center rounded-lg
                                                    text-slate-400 hover:text-red-600 hover:bg-red-50
                                                    transition-colors"
                                                title="Delete">

                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-24 text-center">
                                <div class="max-w-sm mx-auto">
                                    <div class="mx-auto w-16 h-16 rounded-2xl bg-slate-100
                                                flex items-center justify-center mb-5">

                                        <i class="fas fa-inbox text-2xl text-slate-300"></i>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-800">
                                        No posts found
                                    </h3>
                                    <p class="text-sm text-slate-400 mt-1.5">
                                        There are no posts in this category yet.
                                    </p>
                                    <a href="{{ route('admin.posts.create') }}"
                                    class="mt-5 inline-flex items-center gap-2 px-4 py-2.5
                                            bg-slate-900 text-white rounded-xl text-sm font-semibold
                                            hover:bg-slate-800 transition-colors">

                                        <i class="fas fa-plus text-xs"></i>
                                        Create Post
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($posts->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $posts->links() }}
            </div>
        @endif
    </div>


    {{-- Mobile Post List --}}
    <div class="post-mobile-list space-y-3">
        @forelse($posts as $post)
            @php
                $style = $typeStyles[$post->type] ?? [
                    'bg' => 'bg-slate-50',
                    'text' => 'text-slate-700',
                    'ring' => 'ring-slate-200',
                    'dot' => 'bg-slate-400',
                ];

                $typeName = \App\Models\Post::typeLabel($post->type);
                $imageUrl = $post->image_json['url'] ?? null;
            @endphp

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden"
                onclick="window.location='{{ route('admin.posts.edit', $post) }}'">
                <div class="p-4">
                    <div class="flex gap-3">
                        <div class="post-thumb relative w-12 h-12 rounded-xl overflow-hidden shrink-0
                                    bg-slate-50 ring-1 ring-slate-200 flex items-center justify-center">
                            @if($imageUrl)
                                <img src="{{ $imageUrl }}"
                                    alt=""
                                    class="w-full h-full object-cover">
                            @else
                                <i class="fas {{ \App\Models\Post::typeIcon($post->type) }}
                                text-indigo-400 text-sm"></i>
                            @endif

                            @if($post->is_urgent)
                                <span class="absolute top-1 right-1 w-2.5 h-2.5 rounded-full
                                            bg-rose-500 ring-2 ring-white"></span>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="font-semibold text-slate-800 leading-snug">
                                    {{ $post->title }}
                                </h3>
                                <i class="fas fa-chevron-right text-[10px] text-slate-300 mt-1"></i>
                            </div>

                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md
                                            text-[10px] font-semibold ring-1 ring-inset
                                            {{ $style['bg'] }}
                                            {{ $style['text'] }}
                                            {{ $style['ring'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $style['dot'] }}"></span>
                                    {{ $typeName }}
                                </span>
                                @if($post->is_active)
                                    <span class="inline-flex items-center gap-1.5 text-[10px]
                                                font-bold text-emerald-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Live
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-[10px]
                                                font-bold text-slate-400">

                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Draft
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-4 pt-3 border-t border-slate-100">
                        <div class="flex items-center gap-3 text-[11px] text-slate-400">
                            @if($post->published_at)
                                <span>
                                    <i class="far fa-calendar mr-1"></i>
                                    {{ $post->published_at->format('M d, Y') }}
                                </span>
                            @endif

                            @if($post->artifacts->count())
                                <span>
                                    <i class="fas fa-paperclip mr-1"></i>
                                    {{ $post->artifacts->count() }}
                                </span>
                            @endif

                            @if($post->type === 'document' && $post->class_label)
                                <span>
                                    <i class="fas fa-graduation-cap mr-1"></i>
                                    {{ $post->class_label }}
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-1"
                            onclick="event.stopPropagation()">

                            <a href="{{ route('admin.posts.edit', $post) }}"
                            class="w-8 h-8 rounded-lg flex items-center justify-center
                                    text-slate-400 hover:text-indigo-600 hover:bg-indigo-50">

                                <i class="fas fa-pen text-[10px]"></i>
                            </a>

                            <form action="{{ route('admin.posts.destroy', $post) }}"
                                method="POST"
                                onsubmit="return confirm('Delete this post?')">

                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center
                                            text-slate-400 hover:text-red-600 hover:bg-red-50">

                                    <i class="fas fa-trash-alt text-[10px]"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100
                            flex items-center justify-center mb-4">

                    <i class="fas fa-inbox text-xl text-slate-300"></i>

                </div>
                <h3 class="font-bold text-slate-800">
                    No posts found
                </h3>
                <p class="text-sm text-slate-400 mt-1">
                    Create your first post to get started.
                </p>
                <a href="{{ route('admin.posts.create') }}"
                class="mt-4 inline-flex items-center gap-2 px-4 py-2.5
                        rounded-xl bg-slate-900 text-white text-sm font-semibold">

                    <i class="fas fa-plus text-xs"></i>
                    Create Post
                </a>
            </div>
        @endforelse

        @if($posts->hasPages())
            <div class="pt-2">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
