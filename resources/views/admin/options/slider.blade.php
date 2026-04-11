@extends('layouts.admin')

@section('title', 'Manage Sliders')

@push('header_actions')
<button type="submit" form="slider-form" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
    <i class="fas fa-save mr-2"></i>
    Save Sliders
</button>
@endpush

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-8 text-gray-900">
            <form id="slider-form" action="{{ route('admin.sliders.update') }}" method="POST" enctype="multipart/form-data" x-data="{ 
                newSliders: [],
                addSlider() {
                    this.newSliders.push({title: '', order: (this.newSliders.length + {{ count($sliders) }})});
                },
                removeNew(index) {
                    this.newSliders.splice(index, 1);
                }
            }">
                @csrf
                <!-- Existing (Active) Sliders Section -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                        <div class="flex items-center gap-4">
                            <h2 class="text-lg font-bold text-slate-900 border-r border-slate-200 pr-4">Active Hero Sliders</h2>
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-wider rounded-full shadow-sm">
                                {{ count($sliders) }} Current Slides
                            </span>
                        </div>
                        <button type="button" @click="addSlider()" 
                                class="inline-flex items-center px-6 py-2 bg-emerald-600 text-white rounded-xl text-xs font-black shadow-lg shadow-emerald-100 hover:bg-emerald-700 hover:scale-105 active:scale-95 transition-all">
                            <i class="fas fa-plus-circle mr-2"></i> ADD NEW SLIDE
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @forelse($sliders as $index => $item)
                        <div class="relative group" x-data="{ isDeleted: false }" x-show="!isDeleted">
                            <div class="bg-white border border-slate-200 rounded-3xl shadow-sm hover:shadow-xl hover:border-indigo-200 transition-all duration-300 overflow-hidden">
                                
                                <div class="relative h-44 overflow-hidden">
                                    <img src="{{ $item['url'] }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                    <div class="absolute inset-0 bg-linear-to-t from-black/80 via-black/20 to-transparent flex items-end p-5">
                                        <div class="w-full">
                                            <div class="flex items-center justify-between">
                                                <div class="flex flex-col">
                                                    <span class="text-[10px] font-black text-white/60 uppercase tracking-widest mb-1">Slide Index</span>
                                                    <span class="text-white font-black text-lg">#{{ $index + 1 }}</span>
                                                </div>
                                                <button type="button" @click="isDeleted = true" class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center hover:bg-red-500 hover:scale-110 transition-all shadow-lg">
                                                    <i class="fas fa-trash-alt text-white text-sm"></i>
                                                </button>
                                                <input type="hidden" name="existing_sliders[{{ $index }}][delete]" :value="isDeleted ? '1' : '0'">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-2 space-y-5">
                                    <input type="hidden" name="existing_sliders[{{ $index }}][url]" value="{{ $item['url'] }}">
                                    <input type="hidden" name="existing_sliders[{{ $index }}][path]" value="{{ $item['path'] }}">

                                    <div class="space-y-1">
                                        <input type="text" name="existing_sliders[{{ $index }}][title]" value="{{ $item['title'] ?? '' }}"  placeholder="Caption text..."
                                               class="w-full bg-slate-50 border-slate-100 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 placeholder:text-slate-300 p-4 transition-all">
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-1">
                                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.15em] flex items-center">
                                                <i class="fas fa-sort mr-2 text-indigo-400"></i> Sequence
                                            </label>
                                            <input type="number" name="existing_sliders[{{ $index }}][order]" value="{{ $item['order'] ?? 0 }}" 
                                                   class="w-full bg-slate-50 border-slate-100 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 p-4 transition-all">
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.15em] flex items-center">
                                                <i class="fas fa-sync mr-2 text-indigo-400"></i> Change
                                            </label>
                                            <div class="relative h-13">
                                                <input type="file" name="existing_sliders[{{ $index }}][image]" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                                <div class="w-full h-full flex items-center justify-center bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-2xl border border-indigo-100 hover:bg-indigo-600 hover:text-white transition-all duration-300 shadow-sm">
                                                    SELECT FILE
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <!-- Empty state handled within the same grid if needed -->
                        @endforelse

                        <!-- Dynamic New Slider Slots -->
                        <template x-for="(slider, index) in newSliders" :key="index">
                            <div class="relative group animate-in fade-in zoom-in duration-300">
                                <div class="bg-emerald-50/50 border-2 border-dashed border-emerald-200 rounded-3xl shadow-sm hover:shadow-xl hover:border-emerald-400 transition-all duration-300 overflow-hidden">
                                    
                                    <div class="relative h-44 bg-emerald-100 flex items-center justify-center overflow-hidden">
                                        <div class="text-center group-hover:scale-110 transition-transform">
                                            <i class="fas fa-cloud-upload-alt text-emerald-400 text-4xl mb-2"></i>
                                            <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">New Slot Active</p>
                                        </div>
                                        
                                        <div class="absolute top-4 right-4">
                                            <button type="button" @click="removeNew(index)" class="w-8 h-8 rounded-xl bg-white text-red-500 shadow-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="p-6 space-y-5 bg-white">
                                        <div class="space-y-1">
                                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.15em] flex items-center">
                                                <i class="fas fa-heading mr-2 text-emerald-500"></i> New Caption
                                            </label>
                                            <input type="text" :name="`new_slider_titles[${index}]`" x-model="slider.title" placeholder="Welcome text..."
                                                   class="w-full bg-slate-50 border-slate-100 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 p-4 transition-all">
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="space-y-1">
                                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.15em] flex items-center">
                                                    <i class="fas fa-sort mr-2 text-emerald-500"></i> Order
                                                </label>
                                                <input type="number" :name="`new_slider_orders[${index}]`" x-model="slider.order"
                                                       class="w-full bg-slate-50 border-slate-100 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 p-4">
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.15em] flex items-center">
                                                    <i class="fas fa-image mr-2 text-emerald-500"></i> Source
                                                </label>
                                                <div class="relative h-13">
                                                    <input type="file" name="new_sliders[]" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                                    <div class="w-full h-full flex items-center justify-center bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-emerald-200 hover:bg-emerald-700 transition-all duration-300">
                                                        UPLOAD
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        @if(count($sliders) == 0)
                        <div x-show="newSliders.length == 0" class="col-span-full border-2 border-dashed border-slate-200 py-16 rounded-3xl text-center bg-slate-50/50 animate-pulse">
                            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                <i class="fas fa-images text-slate-300 text-3xl"></i>
                            </div>
                            <p class="text-slate-400 text-sm font-black uppercase tracking-[0.2em]">Zero Slides Found</p>
                            <p class="text-xs text-slate-400 mt-2 italic">Click "+ ADD NEW SLIDE" to begin.</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Removed New Sliders Section from bottom -->
            </form>
        </div>
    </div>
</div>
@endsection
