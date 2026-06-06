@extends('layouts.app')

@section('title', 'Academic Rules')

@section('content')
<section class="py-16">
    <div class="mx-auto max-w-[90%] px-4 sm:px-6 lg:px-8">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.35em] text-slate-500">Academic</p>
            <h1 class="mt-4 text-4xl font-black text-slate-950">Academic Rules & Regulations</h1>

            <div class="mt-12 bg-white rounded-3xl border border-slate-100 p-10 shadow-sm">
                <div class="prose prose-slate max-w-none prose-headings:font-black prose-headings:text-slate-950 prose-p:text-slate-600 prose-p:font-medium">
                    <h2 class="text-2xl font-black text-slate-950 mb-6">Standard Operating Procedures</h2>
                    
                    <div class="grid md:grid-cols-2 gap-10">
                        <div>
                            <h3 class="text-lg font-black text-slate-950 mb-4 flex items-center">
                                <span class="w-8 h-8 rounded-lg bg-accent/10 text-accent flex items-center justify-center mr-3 text-sm">01</span>
                                Attendance Policy
                            </h3>
                            <p>Students must maintain at least 85% attendance to be eligible for final examinations. Any absence must be justified with a written application from parents or guardians.</p>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-black text-slate-950 mb-4 flex items-center">
                                <span class="w-8 h-8 rounded-lg bg-accent/10 text-accent flex items-center justify-center mr-3 text-sm">02</span>
                                Uniform & Discipline
                            </h3>
                            <p>Students must attend the school in the prescribed uniform. Punctuality is strictly enforced, and latecomers may be denied entry after the morning assembly.</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-black text-slate-950 mb-4 flex items-center">
                                <span class="w-8 h-8 rounded-lg bg-accent/10 text-accent flex items-center justify-center mr-3 text-sm">03</span>
                                Examination Rules
                            </h3>
                            <p>Malpractice during examinations will result in immediate expulsion from the exam hall and potential suspension. Use of electronic devices is strictly prohibited.</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-black text-slate-950 mb-4 flex items-center">
                                <span class="w-8 h-8 rounded-lg bg-accent/10 text-accent flex items-center justify-center mr-3 text-sm">04</span>
                                Laboratory Etiquette
                            </h3>
                            <p>Students must follow all safety protocols in science and computer labs. Any damage to equipment due to negligence will be the responsibility of the student.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
