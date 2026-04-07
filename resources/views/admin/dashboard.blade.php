@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="text-sm text-slate-500 mt-1">Here's what's happening at your institution today.</p>
        </div>
        <div class="text-sm text-slate-500">
            {{ now()->format('l, F j, Y') }}
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Notices Stat -->
        <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm flex items-center justify-between group hover:border-indigo-100 transition-colors">
            <div>
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Active Notices</div>
                <div class="text-3xl font-bold text-slate-800">{{ \App\Models\Notice::count() }}</div>
            </div>
            <div class="w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                <i class="fas fa-bullhorn fa-lg"></i>
            </div>
        </div>

        <!-- Speeches Stat -->
        <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm flex items-center justify-between group hover:border-emerald-100 transition-colors">
            <div>
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Total Speeches</div>
                <div class="text-3xl font-bold text-slate-800">{{ \App\Models\Speech::count() }}</div>
            </div>
            <div class="w-12 h-12 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-500 group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                <i class="fas fa-microphone-alt fa-lg"></i>
            </div>
        </div>
        
        <!-- News Stat -->
        <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm flex items-center justify-between group hover:border-blue-100 transition-colors">
            <div>
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Total News</div>
                <div class="text-3xl font-bold text-slate-800">{{ \App\Models\News::count() ?? 0 }}</div>
            </div>
            <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500 group-hover:bg-blue-500 group-hover:text-white transition-colors">
                <i class="fas fa-newspaper fa-lg"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm flex items-center justify-between group hover:border-amber-100 transition-colors">
            <div>
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Sliders Active</div>
                <div class="text-3xl font-bold text-slate-800">3</div> <!-- Demo static to match others -->
            </div>
            <div class="w-12 h-12 rounded-lg bg-amber-50 flex items-center justify-center text-amber-500 group-hover:bg-amber-500 group-hover:text-white transition-colors">
                <i class="fas fa-images fa-lg"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Quick Actions -->
        <div class="col-span-1 border border-slate-100 bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest mb-4">Quick Links</h3>
            <div class="space-y-3">
                <a href="{{ route('admin.settings.index') }}" class="flex items-center p-3 rounded-lg hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100 group">
                    <div class="w-8 h-8 rounded-md bg-slate-100 flex items-center justify-center text-slate-500 group-hover:bg-white group-hover:text-indigo-500 transition-colors mr-3">
                        <i class="fas fa-university"></i>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-slate-800">Institution Settings</div>
                        <div class="text-xs text-slate-500">Manage basic info</div>
                    </div>
                    <i class="fas fa-chevron-right ml-auto text-xs text-slate-300 group-hover:text-indigo-400"></i>
                </a>
                
                <a href="{{ route('admin.notices.create') }}" class="flex items-center p-3 rounded-lg hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100 group">
                    <div class="w-8 h-8 rounded-md bg-slate-100 flex items-center justify-center text-slate-500 group-hover:bg-white group-hover:text-emerald-500 transition-colors mr-3">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-slate-800">Publish Notice</div>
                        <div class="text-xs text-slate-500">Announce updates</div>
                    </div>
                    <i class="fas fa-chevron-right ml-auto text-xs text-slate-300 group-hover:text-emerald-400"></i>
                </a>
            </div>
        </div>

        <!-- Recent Activities (Placeholder) -->
        <div class="col-span-1 md:col-span-2 border border-slate-100 bg-white rounded-xl shadow-sm p-6">
             <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest">Recent Activities</h3>
             </div>
             <div class="flex flex-col items-center justify-center h-48 text-center text-slate-400 space-y-3">
                 <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center">
                    <i class="fas fa-chart-line text-2xl text-slate-300"></i>
                 </div>
                 <p class="text-sm">No recent activities available yet.</p>
             </div>
        </div>
    </div>
</div>
@endsection
