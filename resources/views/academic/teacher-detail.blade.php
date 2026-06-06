@extends('layouts.app')

@section('title', $teacher->teacher_name)

@section('content')
<section class="py-16">
    <div class="mx-auto max-w-[90%] px-4 sm:px-6 lg:px-8">
        <div class="rounded-4xl bg-white p-8 lg:p-12 shadow-sm ring-1 ring-slate-200">
            <!-- Header Back Navigation -->
            <div class="mb-12">
                <a href="{{ route('teachers.index') }}" class="group inline-flex items-center gap-2 text-slate-500 hover:text-accent font-bold text-sm transition-colors">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                    Back to Teachers list
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
                <!-- Profile Image & Quick Facts -->
                <div class="lg:col-span-4 lg:col-start-1">
                    <div class="sticky top-8 space-y-10">
                        <!-- Profile Card -->
                        <div class="relative group rounded-4xl overflow-hidden shadow-2xl bg-slate-100 aspect-square">
                            @if($teacher->gender == 'female')
                                <img src="{{ asset('images/female-teacher.png') }}" 
                                     alt="{{ $teacher->teacher_name }}" 
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            @elseif($teacher->photo)
                                <img src="{{ $teacher->photo }}" 
                                     alt="{{ $teacher->teacher_name }}" 
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-slate-200">
                                    <i class="fas fa-user-tie text-slate-300 text-9xl"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Basic Details -->
                        <div class="bg-slate-50 p-8 rounded-4xl border border-slate-100 shadow-sm">
                            <h3 class="text-sm font-black text-slate-950 uppercase tracking-[0.2em] mb-6 pb-2 border-b-2 border-accent inline-block">Quick Information</h3>
                            <div class="space-y-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-2xl bg-white flex items-center justify-center text-accent shrink-0 shadow-sm border border-slate-100">
                                        <i class="fas fa-envelope text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Email Address</p>
                                        <p class="text-sm font-bold text-slate-900 truncate">{{ $teacher->email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-2xl bg-white flex items-center justify-center text-accent shrink-0 shadow-sm border border-slate-100">
                                        <i class="fas fa-phone-alt text-xs"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Phone Number</p>
                                        <p class="text-sm font-bold text-slate-900">{{ $teacher->phone ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-2xl bg-white flex items-center justify-center text-accent shrink-0 shadow-sm border border-slate-100">
                                        <i class="fas fa-id-badge text-xs"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Teacher ID</p>
                                        <p class="text-sm font-bold text-slate-900">{{ $teacher->teacher_code ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-2xl bg-white flex items-center justify-center text-accent shrink-0 shadow-sm border border-slate-100">
                                        <i class="fas fa-calendar-check text-xs"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Joined On</p>
                                        <p class="text-sm font-bold text-slate-900">{{ $teacher->joining_date ? $teacher->joining_date->format('M d, Y') : 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Biography and Stats -->
                <div class="lg:col-span-8">
                    <div class="space-y-16">
                        <!-- Identity Section -->
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.35em] text-accent">{{ $teacher->department ?? 'Academic Faculty' }}</p>
                            <h1 class="mt-4 text-5xl font-black text-slate-950">{{ $teacher->teacher_name }}</h1>
                            <div class="mt-6 flex items-center gap-4">
                                <span class="px-6 py-2 bg-slate-900 text-white text-xs font-black uppercase tracking-widest rounded-full">{{ $teacher->designation }}</span>
                                @if($teacher->mpo)
                                    <span class="px-6 py-2 bg-emerald-100 text-emerald-700 text-xs font-black uppercase tracking-widest rounded-full">MPO Certified</span>
                                @endif
                                @if($teacher->experience_years)
                                    <span class="text-sm font-bold text-slate-500 italic">{{ $teacher->experience_years }}+ Years Experience</span>
                                @endif
                            </div>
                        </div>

                        <!-- Academic Qualifications -->
                        @if($teacher->qualifications->isNotEmpty())
                        <div>
                            <h2 class="text-2xl font-black text-slate-950 mb-8 pb-3 border-b-2 border-accent inline-block">Education & Qualifications</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($teacher->qualifications as $qual)
                                <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 hover:bg-white hover:shadow-xl transition-all group">
                                    <div class="flex gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-accent shadow-sm group-hover:bg-accent group-hover:text-white transition-colors">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-black text-slate-900 text-lg">{{ $qual->degree }}</h4>
                                            <p class="text-sm font-bold text-slate-500 mt-1 uppercase tracking-widest">{{ $qual->passing_year }}</p>
                                            <p class="text-sm font-semibold text-slate-400 mt-2">{{ $qual->institution }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Training & Professional Development -->
                        @if($teacher->trainings->isNotEmpty())
                        <div>
                            <h2 class="text-2xl font-black text-slate-950 mb-8 pb-3 border-b-2 border-accent inline-block">Professional Training</h2>
                            <div class="space-y-4">
                                @foreach($teacher->trainings as $training)
                                <div class="flex gap-6 p-6 items-center">
                                    <div class="w-2 h-2 rounded-full bg-accent animate-pulse"></div>
                                    <div class="flex-1">
                                        <h4 class="font-black text-slate-900 text-lg leading-tight">{{ $training->title }}</h4>
                                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mt-2">{{ $training->year }} — {{ $training->institution }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Additional Information -->
                        <div>
                            <h2 class="text-2xl font-black text-slate-950 mb-8 pb-3 border-b-2 border-accent inline-block">Institutional Presence</h2>
                            <div class="prose prose-slate prose-lg max-w-none text-slate-600 italic">
                                <p>{{ $teacher->teacher_name }} is a dedicated member of our academic team, currently serving as a {{ $teacher->designation }} in the {{ $teacher->department ?? 'General Education' }} department. They contribute significantly to the holistic growth of our students and the excellence of our institution.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection