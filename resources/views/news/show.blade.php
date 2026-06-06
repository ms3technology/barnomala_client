@extends('layouts.app')

@section('title', $news->title)

@section('content')
<section class="py-16">
    <div class="mx-auto max-w-[90%] px-4 sm:px-6 lg:px-8">
        <div>
            <div class="mb-12">
                <p class="text-[10px] font-black uppercase tracking-[0.35em] text-accent mb-4 flex items-center gap-2">
                    <i class="far fa-calendar-alt"></i>
                    Published: {{ $news->published_at ? $news->published_at->format('d M, Y') : 'N/A' }}
                </p>
                <h1 class="text-3xl md:text-5xl font-black text-slate-950 leading-tight mb-8">{{ $news->title }}</h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
                <!-- Main Content -->
                <div class="lg:col-span-8">
                    @if($news->image_json)
                        <div class="aspect-video rounded-3xl overflow-hidden shadow-2xl shadow-slate-200/50 mb-12 border border-slate-100 ring-4 ring-white">
                            <img src="{{ $news->image_url }}" alt="{{ $news->title }}" class="w-full h-full object-cover">
                        </div>
                    @endif

                    <div class="prose prose-slate prose-lg max-w-none text-slate-700 leading-relaxed font-medium">
                        {!! nl2br($news->content) !!}
                    </div>

                    @php
                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                        $imageArtifacts = $news->artifacts->filter(fn($a) => in_array(strtolower(pathinfo($a->file_path, PATHINFO_EXTENSION)), $imageExtensions));
                        $otherArtifacts = $news->artifacts->filter(fn($a) => !in_array(strtolower(pathinfo($a->file_path, PATHINFO_EXTENSION)), $imageExtensions));
                    @endphp

                    @if($imageArtifacts->isNotEmpty())
                        <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 gap-6">
                            @foreach($imageArtifacts as $image)
                                <div class="rounded-3xl overflow-hidden shadow-xl border border-slate-100 group">
                                    <a href="{{ Storage::url($image->file_path) }}" target="_blank">
                                        <img src="{{ Storage::url($image->file_path) }}" alt="{{ $image->file_name }}" class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-500">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($otherArtifacts->count())
                        <div class="mt-16 pt-12 border-t border-slate-100">
                            <h3 class="text-xl font-black text-slate-950 mb-8 flex items-center gap-3">
                                <i class="fas fa-paperclip text-accent"></i> Attached Documents
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($otherArtifacts as $artifact)
                                    <a href="{{ Storage::url($artifact->file_path) }}" target="_blank" 
                                       class="p-5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between group hover:bg-white hover:border-accent hover:shadow-xl hover:shadow-accent/5 hover:-translate-y-1 transition-all duration-300">
                                        <div class="flex items-center gap-4 overflow-hidden">
                                            @php
                                                $ext = strtolower(pathinfo($artifact->file_path, PATHINFO_EXTENSION));
                                                $icon = match($ext) {
                                                    'pdf' => 'fa-file-pdf',
                                                    'doc', 'docx' => 'fa-file-word',
                                                    'xls', 'xlsx' => 'fa-file-excel',
                                                    'ppt', 'pptx' => 'fa-file-powerpoint',
                                                    'zip', 'rar' => 'fa-file-archive',
                                                    default => 'fa-file',
                                                };
                                            @endphp
                                            <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center text-accent shadow-sm ring-1 ring-slate-200 group-hover:bg-accent group-hover:text-white group-hover:ring-accent transition-all">
                                                <i class="fas {{ $icon }} text-xl"></i>
                                            </div>
                                            <div class="overflow-hidden">
                                                <p class="text-sm font-black text-slate-900 truncate">{{ $artifact->file_name }}</p>
                                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ round($artifact->file_size / 1024, 1) }} KB</p>
                                            </div>
                                        </div>
                                        <i class="fas fa-arrow-right text-[10px] text-slate-300 group-hover:text-accent group-hover:translate-x-1 transition-all"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-4 mt-12 lg:mt-0 space-y-12">
                    <div class="rounded-3xl bg-slate-50 p-8 border border-slate-100 sticky top-24">
                        <h3 class="text-lg font-black text-slate-950 mb-6 flex items-center gap-3">
                            <span class="w-2 h-8 bg-accent rounded-full"></span>
                            Recent Stories
                        </h3>
                        <div class="space-y-8">
                            @forelse ($recentNews as $recent)
                                <div class="flex gap-4 group cursor-pointer">
                                    <div class="w-20 h-20 shrink-0 rounded-2xl overflow-hidden shadow-sm border border-slate-100">
                                        <img src="{{ $recent->image_url }}" alt="{{ $recent->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2">{{ $recent->published_at ? $recent->published_at->format('M d, Y') : 'N/A' }}</p>
                                        <h4 class="text-sm font-black text-slate-900 leading-snug line-clamp-2 group-hover:text-accent transition-colors">
                                            <a href="{{ route('news.show', $recent) }}">
                                                {{ $recent->title }}
                                            </a>
                                        </h4>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500 font-medium">No other news found.</p>
                            @endforelse
                        </div>

                        <div class="mt-12">
                            <a href="{{ route('news.index') }}" class="w-full flex items-center justify-center py-4 rounded-xl bg-slate-950 text-white text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-slate-900/10 hover:bg-slate-800 transition-all">
                                View Library
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
