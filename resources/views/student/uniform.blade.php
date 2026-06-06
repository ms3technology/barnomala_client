@extends('layouts.app')

@section('title', 'Student Uniform')

@section('content')
<section class="py-16">
    <div class="mx-auto max-w-[90%] px-4 sm:px-6 lg:px-8">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.35em] text-slate-500">Student</p>
            <h1 class="mt-4 text-4xl font-black text-slate-950">Student Uniform</h1>

            <div class="mt-12 bg-white rounded-3xl border border-slate-100 p-8 shadow-sm">
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-tshirt text-slate-300 text-4xl"></i>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900">Uniform Details Coming Soon</h2>
                    <p class="mt-4 text-slate-500 max-w-md mx-auto">Detailed information about the required school uniform for the current session is being updated.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
