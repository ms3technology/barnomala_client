@extends('layouts.app')

@section('title', $photo->title)

@section('content')
<section class="py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-4xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.35em] text-slate-500">{{ $photo->category ?? 'General' }}</p>
                    <h1 class="mt-4 text-4xl font-black text-slate-950">{{ $photo->title }}</h1>
                    <p class="mt-2 text-sm font-bold text-accent italic">{{ $photo->date ? $photo->date->format('F d, Y') : '' }}</p>
                </div>
                <a href="{{ route('gallery.index') }}" class="group inline-flex items-center gap-2 px-6 py-3 bg-slate-100 text-slate-600 rounded-2xl hover:bg-slate-200 transition-colors font-bold text-sm">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                    Back to Gallery
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                <!-- Image Side -->
                <div class="lg:col-span-8">
                    <div class="relative group rounded-3xl overflow-hidden shadow-2xl bg-slate-100">
                        <img src="{{ asset('storage/' . $photo->image_path) }}" 
                             alt="{{ $photo->title }}" 
                             class="w-full h-auto object-cover transition-transform duration-700 group-hover:scale-105">
                    </div>
                </div>

                <!-- Content Side -->
                <div class="lg:col-span-4">
                    <div class="sticky top-8 space-y-8">
                        <div>
                            <h2 class="text-xl font-black text-slate-950 mb-4 pb-2 border-b-2 border-accent inline-block">Description</h2>
                            <div class="prose prose-slate prose-lg max-w-none text-slate-600 leading-relaxed italic">
                                {!! nl2br(e($photo->description ?? 'No description available for this visual record.')) !!}
                            </div>
                        </div>

                        <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                            <h3 class="text-sm font-black text-slate-950 uppercase tracking-widest mb-4">Share this journey</h3>
                            <div class="flex gap-3">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center hover:opacity-80 transition-opacity">
                                    <i class="fab fa-facebook-f text-sm"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($photo->title) }}" target="_blank" class="w-10 h-10 rounded-full bg-sky-500 text-white flex items-center justify-center hover:opacity-80 transition-opacity">
                                    <i class="fab fa-twitter text-sm"></i>
                                </a>
                                <button onclick="navigator.clipboard.writeText('{{ request()->fullUrl() }}')" class="w-10 h-10 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center hover:bg-slate-300 transition-colors">
                                    <i class="fas fa-link text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection