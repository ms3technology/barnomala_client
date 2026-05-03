@extends('layouts.app')

@section('title', $staff->name)

@section('content')
<section class="py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-4xl bg-white p-8 lg:p-12 shadow-sm ring-1 ring-slate-200">
            <!-- Header Back Navigation -->
            <div class="mb-12">
                <a href="{{ route('staff.index') }}" class="group inline-flex items-center gap-2 text-slate-500 hover:text-accent font-bold text-sm transition-colors">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                    Back to Staff list
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
                <!-- Profile Image & Quick Facts -->
                <div class="lg:col-span-4 lg:col-start-1">
                    <div class="sticky top-8 space-y-10">
                        <!-- Profile Card -->
                        <div class="relative group rounded-4xl overflow-hidden shadow-2xl bg-slate-100 aspect-square">
                            @if($staff->photo)
                                <img src="{{ $staff->photo }}" 
                                     alt="{{ $staff->name }}" 
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-slate-200">
                                    <i class="fas fa-user text-slate-300 text-9xl"></i>
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
                                        <p class="text-sm font-bold text-slate-900 truncate">{{ $staff->email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-2xl bg-white flex items-center justify-center text-accent shrink-0 shadow-sm border border-slate-100">
                                        <i class="fas fa-phone-alt text-xs"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Phone Number</p>
                                        <p class="text-sm font-bold text-slate-900">{{ $staff->phone ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-2xl bg-white flex items-center justify-center text-accent shrink-0 shadow-sm border border-slate-100">
                                        <i class="fas fa-id-badge text-xs"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Staff Code</p>
                                        <p class="text-sm font-bold text-slate-900">{{ $staff->staff_code ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-2xl bg-white flex items-center justify-center text-accent shrink-0 shadow-sm border border-slate-100">
                                        <i class="fas fa-calendar-check text-xs"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Joined On</p>
                                        <p class="text-sm font-bold text-slate-900">{{ $staff->joining_date ? $staff->joining_date->format('M d, Y') : 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Biography and Details -->
                <div class="lg:col-span-8">
                    <div class="space-y-16">
                        <!-- Identity Section -->
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.35em] text-accent">{{ $staff->department ?? 'Support Department' }}</p>
                            <h1 class="mt-4 text-5xl font-black text-slate-950">{{ $staff->name }}</h1>
                            <div class="mt-6 flex items-center gap-4">
                                <span class="px-6 py-2 bg-slate-900 text-white text-xs font-black uppercase tracking-widest rounded-full">{{ $staff->designation }}</span>
                                <span class="px-6 py-2 bg-accent/20 text-accent text-xs font-black uppercase tracking-widest rounded-full">{{ ucfirst($staff->status) }}</span>
                            </div>
                        </div>

                        <!-- General Information -->
                        <div>
                            <h2 class="text-2xl font-black text-slate-950 mb-8 pb-3 border-b-2 border-accent inline-block">Staff Details</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-12">
                                <div class="space-y-2">
                                    <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Gender</p>
                                    <p class="text-base font-bold text-slate-700">{{ ucfirst($staff->gender) ?? 'N/A' }}</p>
                                </div>
                                <div class="space-y-2">
                                    <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Religion</p>
                                    <p class="text-base font-bold text-slate-700">{{ $staff->religion ?? 'N/A' }}</p>
                                </div>
                                <div class="space-y-2">
                                    <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Blood Group</p>
                                    <p class="text-base font-bold text-slate-700">{{ $staff->blood_group ?? 'N/A' }}</p>
                                </div>
                                <div class="space-y-2">
                                    <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Marital Status</p>
                                    <p class="text-base font-bold text-slate-700">{{ ucfirst($staff->marital_status) ?? 'N/A' }}</p>
                                </div>
                                <div class="space-y-2">
                                    <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Date of Birth</p>
                                    <p class="text-base font-bold text-slate-700">{{ $staff->date_of_birth ? $staff->date_of_birth->format('M d, Y') : 'N/A' }}</p>
                                </div>
                                <div class="space-y-2">
                                    <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">National ID</p>
                                    <p class="text-base font-bold text-slate-700">{{ $staff->national_id ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Address Section -->
                        <div>
                            <h2 class="text-2xl font-black text-slate-950 mb-8 pb-3 border-b-2 border-accent inline-block">Address Information</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-10 gap-x-16">
                                <div class="p-8 bg-slate-50 border border-slate-100 rounded-4xl flex gap-6">
                                    <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-accent shrink-0 shadow-sm border border-slate-100">
                                        <i class="fas fa-home text-sm"></i>
                                    </div>
                                    <div class="space-y-2 min-w-0">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Present Address</p>
                                        <p class="text-sm font-bold text-slate-700 leading-relaxed">{{ $staff->present_address ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="p-8 bg-slate-50 border border-slate-100 rounded-4xl flex gap-6">
                                    <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-accent shrink-0 shadow-sm border border-slate-100">
                                        <i class="fas fa-building text-sm"></i>
                                    </div>
                                    <div class="space-y-2 min-w-0">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Permanent Address</p>
                                        <p class="text-sm font-bold text-slate-700 leading-relaxed">{{ $staff->permanent_address ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
