@extends('layouts.admin')

@section('title', 'Stats & Demographics')

@section('content')
<div class="space-y-4 animate-fade-in">
    <div class="bg-white/70 backdrop-blur-xl border border-slate-200/60 rounded-2xl p-6 shadow-xl shadow-slate-200/40">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
            <div class="space-y-0.5">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Stats & Demographics</h1>
                <p class="text-xs text-slate-500 font-medium italic">Update institutional statistics and student demographics.</p>
            </div>
            <div>
                <button type="submit" form="statsForm" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-200/50 transition-all transform hover:-translate-y-0.5 active:scale-95 group">
                    <i class="fas fa-save mr-2 group-hover:rotate-12 transition-transform"></i>
                    Update
                </button>
            </div>
        </div>

        <form action="{{ route('admin.stats.update') }}" method="POST" id="statsForm">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Institutional Stats -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 mb-1">
                        <div class="p-2 bg-indigo-50 rounded-xl">
                            <i class="fas fa-chart-line text-indigo-600 text-sm"></i>
                        </div>
                        <h2 class="text-lg font-black text-slate-800 tracking-tight uppercase">Base Stats</h2>
                    </div>

                    <div class="grid grid-cols-1 gap-2">
                        @foreach(['classes_count', 'students_count', 'teachers_count', 'staffs_count'] as $statKey)
                            @php 
                                $stat = $stats->firstWhere('option_key', 'institute.stats.' . $statKey);
                                $value = $stat ? $stat->option_value : 0;
                            @endphp
                            <div class="group flex items-center justify-between border border-slate-200 rounded-xl px-4 py-2 bg-slate-50/40 hover:bg-white hover:shadow-md transition-all duration-200">
                                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest w-1/3">
                                    {{ str_replace('_', ' ', str_replace('_count', '', $statKey)) }}
                                </label>
                                <div class="w-1/2">
                                    <input type="number" 
                                           name="stats[{{ $statKey }}]" 
                                           value="{{ $value }}"
                                           class="block w-full text-sm font-bold rounded-lg border border-slate-300 bg-white/90 px-3 py-1.5 text-slate-800 shadow-sm focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100/50">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Demographics Section -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 mb-1">
                        <div class="p-2 bg-rose-50 rounded-xl">
                            <i class="fas fa-users text-rose-600 text-sm"></i>
                        </div>
                        <h2 class="text-lg font-black text-slate-800 tracking-tight uppercase">Demographics</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-4">
                        @php
                            $demographicPresets = [
                                'classes' => ['Six', 'Seven', 'Eight', 'Nine', 'Ten'],
                                'gender' => ['Male', 'Female', 'Other'],
                                'religion' => ['Islam', 'Hindu', 'Christian', 'Buddhism']
                            ];
                        @endphp

                        @foreach($demographicPresets as $type => $presets)
                        <div class="p-4 border border-slate-200 rounded-2xl bg-slate-50/30">
                            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 border-b border-slate-100 pb-2">{{ $type }} Distribution</h3>

                            <div class="grid grid-cols-2 gap-x-4 gap-y-2">
                                @foreach($presets as $preset)
                                    <div class="flex items-center gap-2 group">
                                        <div class="w-2/5">
                                            <span class="text-[10px] font-bold text-slate-600 uppercase tracking-tight">{{ $preset }}</span>
                                            <input type="hidden" name="demographics[{{ $type }}][keys][]" value="{{ $preset }}">
                                        </div>
                                        <div class="flex-1">
                                            <input type="number" 
                                                   name="demographics[{{ $type }}][values][]" 
                                                   value="{{ $demographics[$type][$preset] ?? 0 }}"
                                                   class="block w-full text-xs font-bold rounded-lg border border-slate-300 bg-white/80 px-2 py-1 text-slate-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-100">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.5s ease-out; }
    .animate-slide-in { animation: slideIn 0.3s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slideIn { from { opacity: 0; transform: translateX(-10px); } to { opacity: 1; transform: translateX(0); } }
</style>
@endsection
