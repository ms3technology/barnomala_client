@extends('layouts.app')

@php
    $aboutImageOption = \App\Models\Option::where('option_key', 'institute.about.image_json')->first();
    $aboutImageUrl = $aboutImageOption ? (json_decode($aboutImageOption->option_value, true)['url'] ?? asset('images/about-image.webp')) : asset('images/about-image.webp');
    $aboutText = $options['institute.about.text'] ?? 'আমাদের শিক্ষা প্রতিষ্ঠান একটি ঐতিহ্যবাহী বিদ্যাপীঠ। দীর্ঘ পথচলায় আমরা অসংখ্য মেধাবী শিক্ষার্থী উপহার দিয়েছি যারা দেশ ও দশের কল্যাণে নিয়োজিত।';
    $aboutTitle = $options['institute.about.title'] ?? 'আমাদের প্রতিষ্ঠান সম্পর্কে';
@endphp

@section('title', 'About Us')

@section('content')
<section class="py-16 bg-slate-50 relative overflow-hidden font-bn">
    <!-- Decorative background -->
    <div class="absolute top-0 right-0 w-1/2 h-1/2 bg-indigo-50/50 rounded-full blur-3xl -mr-64 -mt-64"></div>
    <div class="absolute bottom-0 left-0 w-1/3 h-1/3 bg-blue-50/50 rounded-full blur-3xl -ml-32 -mb-32"></div>

    <div class="mx-auto max-w-[90%] px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <!-- Image Side -->
            <div class="relative group">
                <div class="absolute inset-0 bg-indigo-600 rounded-[3rem] rotate-3 opacity-10 group-hover:rotate-6 transition-transform duration-500"></div>
                <div class="absolute inset-0 bg-accent rounded-[3rem] -rotate-3 opacity-10 group-hover:-rotate-6 transition-transform duration-500"></div>
                
                <div class="relative aspect-4/5 overflow-hidden rounded-[3rem] shadow-2xl border-8 border-white">
                    <img src="{{ $aboutImageUrl }}" 
                         alt="{{ $aboutTitle }}" 
                         class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-linear-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>

                <!-- Floating Stats/Feature -->
                <div class="absolute -bottom-8 -right-8 bg-white p-6 rounded-3xl shadow-xl border border-slate-100 hidden sm:block animate-bounce-slow">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                            <i class="fas fa-graduation-cap text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black text-indigo-600 uppercase tracking-widest leading-none">Established</p>
                            <p class="text-2xl font-black text-slate-950 mt-1">{{ $options['institute.established_year'] ?? 'Since 1990' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Side -->
            <div class="space-y-8">
                <div>
                    <p class="text-indigo-600 font-black uppercase tracking-[0.3em] text-sm mb-4">Learn More</p>
                    <h1 class="text-4xl md:text-5xl font-black text-slate-950 leading-tight">
                        {{ $aboutTitle }}
                    </h1>
                    <div class="mt-6 w-24 h-2 bg-indigo-600 rounded-full"></div>
                </div>

                <div class="prose prose-slate prose-lg max-w-none text-slate-600 leading-relaxed text-justify">
                    {!! nl2br(e($aboutText)) !!}
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-8">
                    <div class="flex items-start gap-4 p-4 rounded-2xl bg-white border border-slate-100 shadow-sm transition-hover">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-950">Expert Teachers</h4>
                            <p class="text-xs text-slate-500 mt-1">High quality education system</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-4 rounded-2xl bg-white border border-slate-100 shadow-sm transition-hover">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-950">ICT Enabled</h4>
                            <p class="text-xs text-slate-500 mt-1">Modern computer lab facilities</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-4 rounded-2xl bg-white border border-slate-100 shadow-sm transition-hover">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-950">Large Library</h4>
                            <p class="text-xs text-slate-500 mt-1">Vast collection of resources</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-4 rounded-2xl bg-white border border-slate-100 shadow-sm transition-hover">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                            <i class="fas fa-swimmer"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-950">Playgrounds</h4>
                            <p class="text-xs text-slate-500 mt-1">Wide space for sports</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
