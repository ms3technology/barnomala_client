@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Dashboard Cards -->
    <div class="bg-white p-6 rounded-lg border border-slate-200 shadow-sm flex items-center">
        <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 mr-4">
            <i class="fas fa-bullhorn fa-lg"></i>
        </div>
        <div>
            <div class="text-sm font-medium text-slate-500 uppercase tracking-wider">Active Notices</div>
            <div class="text-2xl font-bold text-slate-900">{{ \App\Models\Notice::count() }}</div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg border border-slate-200 shadow-sm flex items-center">
        <div class="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 mr-4">
            <i class="fas fa-microphone-alt fa-lg"></i>
        </div>
        <div>
            <div class="text-sm font-medium text-slate-500 uppercase tracking-wider">Total Speeches</div>
            <div class="text-2xl font-bold text-slate-900">{{ \App\Models\Speech::count() }}</div>
        </div>
    </div>
</div>

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900">
        <h1 class="text-2xl font-bold mb-4">Admin Dashboard</h1>
        <p class="mb-6">Welcome back, {{ auth()->user()->name }}!</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-slate-50 p-6 rounded-lg border border-slate-200 shadow-sm">
                <div class="text-slate-500 text-sm font-medium uppercase mb-2">My Profile</div>
                <div class="text-slate-900">
                    <p><strong>Name:</strong> {{ auth()->user()->name }}</p>
                    <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                </div>
            </div>

            <div class="bg-slate-50 p-6 rounded-lg border border-slate-200 shadow-sm">
                <div class="text-slate-500 text-sm font-medium uppercase mb-2">School Settings</div>
                <p class="text-slate-600">Manage institution information and preferences.</p>
                <a href="#" class="mt-4 inline-block text-accent hover:underline">Manage Settings &rarr;</a>
            </div>

            <div class="bg-slate-50 p-6 rounded-lg border border-slate-200 shadow-sm">
                <div class="text-slate-500 text-sm font-medium uppercase mb-2">Recent Activities</div>
                <p class="text-slate-600 italic text-sm">No recent activities available.</p>
            </div>
        </div>
    </div>
</div>
@endsection
