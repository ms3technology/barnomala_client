@extends('layouts.app')

@section('title', 'History')

@section('content')
<section class="py-16">
    <div class="mx-auto max-w-[90%] px-4 sm:px-6 lg:px-8">
        <div class="rounded-4xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm font-semibold uppercase tracking-[0.35em] text-slate-500">About Us</p>
            <h1 class="mt-4 text-4xl font-black text-slate-950">Our History</h1>

            <div class="mt-12 lg:grid lg:grid-cols-12 lg:gap-16">
                <div class="lg:col-span-8">
                    <div class="prose prose-slate prose-lg max-w-none text-slate-600 leading-relaxed">
                        <p class="mb-6">Established with a vision to provide excellence in education, <strong>{{ $options['institute.name'] ?? config('app.name', 'Our Institution') }}</strong> has been a cornerstone of academic growth since its inception.</p>
                        
                        <h2 class="text-2xl font-black text-slate-950 mt-10 mb-6 border-b-2 border-accent inline-block">The Beginning</h2>
                        <p class="mb-6">Our journey began in a small facility with a handful of dedicated teachers and eager students. From those humble beginnings, we have grown into a leading educational institution known for its commitment to holistic development.</p>

                        <h2 class="text-2xl font-black text-slate-950 mt-10 mb-6 border-b-2 border-accent inline-block">Milestones</h2>
                        <ul class="space-y-4 list-none p-0">
                            <li class="flex items-start gap-4">
                                <span class="shrink-0 w-24 font-black text-accent text-xl">2005</span>
                                <span class="pt-1 text-slate-700">Founded with the mission to serve the local community.</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <span class="shrink-0 w-24 font-black text-accent text-xl">2010</span>
                                <span class="pt-1 text-slate-700">Expanded our campus to include high-school facilities.</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <span class="shrink-0 w-24 font-black text-accent text-xl">2015</span>
                                <span class="pt-1 text-slate-700">Achieved national recognition for academic excellence.</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <span class="shrink-0 w-24 font-black text-accent text-xl">2020</span>
                                <span class="pt-1 text-slate-700">Launched our digital transformation initiative.</span>
                            </li>
                        </ul>

                        <h2 class="text-2xl font-black text-slate-950 mt-10 mb-6 border-b-2 border-accent inline-block">Our Future</h2>
                        <p class="mb-6">Looking ahead, we continue to embrace innovation while staying true to our core values. We are committed to nurturing the next generation of leaders and thinkers who will make a positive impact in the world.</p>
                    </div>
                </div>

                <div class="lg:col-span-4 mt-12 lg:mt-0">
                    <div class="sticky top-24 rounded-3xl bg-slate-50 p-8 ring-1 ring-slate-200">
                        <h3 class="text-xl font-black text-slate-950 mb-6">Quick Facts</h3>
                        <div class="space-y-6">
                            <div>
                                <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Established</p>
                                <p class="text-lg font-bold text-slate-950">Since 2005</p>
                            </div>
                            <div class="h-px bg-slate-200"></div>
                            <div>
                                <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Campus size</p>
                                <p class="text-lg font-bold text-slate-950">5 Acres</p>
                            </div>
                            <div class="h-px bg-slate-200"></div>
                            <div>
                                <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Total Students</p>
                                <p class="text-lg font-bold text-slate-950">1,200+</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
