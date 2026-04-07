@extends('layouts.app')

@section('title', 'Achievements')

@section('content')
<section class="py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-4xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm font-semibold uppercase tracking-[0.35em] text-slate-500">Excellence</p>
            <h1 class="mt-4 text-4xl font-black text-slate-950">Achievements & Recognition</h1>

            <div class="mt-12 space-y-24">
                <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 items-center">
                    <div>
                        <div class="aspect-video bg-slate-100 rounded-3xl overflow-hidden shadow-lg ring-1 ring-slate-200 flex items-center justify-center">
                            <i class="fa-solid fa-trophy text-8xl text-accent opacity-20"></i>
                        </div>
                    </div>
                    <div>
                        <span class="inline-flex rounded-full bg-accent/10 px-4 py-2 text-sm font-black text-accent uppercase tracking-wider mb-6">Academic Excellence</span>
                        <h2 class="text-3xl font-black text-slate-950 mb-6">Top Academic Performance 2024</h2>
                        <p class="text-xl text-slate-600 leading-relaxed">Our students have consistently topped the national examinations, reflecting the quality of education and dedication of our faculty. We are proud of our 100% pass rate for the 10th consecutive year.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 items-center lg:flex-row-reverse">
                    <div class="lg:order-last">
                        <div class="aspect-video bg-slate-100 rounded-3xl overflow-hidden shadow-lg ring-1 ring-slate-200 flex items-center justify-center">
                            <i class="fa-solid fa-medal text-8xl text-accent opacity-20"></i>
                        </div>
                    </div>
                    <div>
                        <span class="inline-flex rounded-full bg-accent/10 px-4 py-2 text-sm font-black text-accent uppercase tracking-wider mb-6">Sports & Athletics</span>
                        <h2 class="text-3xl font-black text-slate-950 mb-6">Regional Swimming Champions</h2>
                        <p class="text-xl text-slate-600 leading-relaxed">Beyond academics, our students excel in various sporting disciplines. Our swimming team recently secured the regional championship trophy for the third year in a row.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 items-center">
                    <div>
                        <div class="aspect-video bg-slate-100 rounded-3xl overflow-hidden shadow-lg ring-1 ring-slate-200 flex items-center justify-center">
                            <i class="fa-solid fa-flask text-8xl text-accent opacity-20"></i>
                        </div>
                    </div>
                    <div>
                        <span class="inline-flex rounded-full bg-accent/10 px-4 py-2 text-sm font-black text-accent uppercase tracking-wider mb-6">Innovation</span>
                        <h2 class="text-3xl font-black text-slate-950 mb-6">National Science Fair Award</h2>
                        <p class="text-xl text-slate-600 leading-relaxed">Our students' research project on sustainable energy was awarded the first prize at the National Science Fair, highlighting our focus on innovation and environmental consciousness.</p>
                    </div>
                </div>
            </div>

            <div class="mt-24 pt-16 border-t border-slate-200">
                <h3 class="text-3xl font-black text-slate-950 text-center mb-16 underline decoration-accent underline-offset-8">Our Recognition Wall</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    @for ($i = 0; $i < 4; $i++)
                        <div class="bg-slate-50 p-6 rounded-3xl ring-1 ring-slate-200 text-center group transition duration-300 hover:bg-white hover:shadow-xl hover:-translate-y-2">
                            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm ring-1 ring-slate-200 group-hover:bg-accent group-hover:text-white transition group-duration-300">
                                <i class="fa-solid fa-award text-2xl"></i>
                            </div>
                            <h4 class="font-black text-lg text-slate-950 mb-2">Award Title {{ $i+1 }}</h4>
                            <p class="text-slate-500 text-sm">Recognizing our commitment to excellence in 202{{ $i+1 }}.</p>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
