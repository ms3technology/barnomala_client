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
        <div class="p-8 text-gray-900 px-12">
            <h1 class="text-2xl font-bold text-slate-900 mb-8 flex items-center">
                <i class="fas fa-images mr-3 text-emerald-600"></i>
                Home Slider Management
            </h1>

            <form id="slider-form" action="{{ route('admin.sliders.update') }}" method="POST" enctype="multipart/form-data" x-data="{ 
                newSliders: [],
                addSlider() {
                    this.newSliders.push({title: '', order: 0});
                },
                removeNew(index) {
                    this.newSliders.splice(index, 1);
                }
            }">
                @csrf

                <!-- Slider Layout Option -->
                <div class="mb-10 p-6 bg-indigo-50 border border-indigo-100 rounded-2xl">
                    <h2 class="text-sm font-black text-indigo-900 uppercase tracking-widest mb-4 flex items-center">
                        <i class="fas fa-th-large mr-2"></i> Hero Section Layout
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="relative flex items-center p-4 bg-white border border-slate-200 rounded-xl cursor-pointer hover:border-indigo-300 transition-colors">
                            <input type="radio" name="hero_type" value="slider_only" {{ ($options['institute.hero.type'] ?? '') == 'slider_only' ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <div class="ml-4">
                                <span class="block text-sm font-bold text-slate-900">Standard Slider</span>
                                <span class="block text-xs text-slate-500">Standard full width slider layout.</span>
                            </div>
                        </label>
                        <label class="relative flex items-center p-4 bg-white border border-slate-200 rounded-xl cursor-pointer hover:border-indigo-300 transition-colors">
                            <input type="radio" name="hero_type" value="slider_with_notice" {{ ($options['institute.hero.type'] ?? 'slider_with_notice') == 'slider_with_notice' ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <div class="ml-4">
                                <span class="block text-sm font-bold text-slate-900">With Notice Panel</span>
                                <span class="block text-xs text-slate-500">Slider alongside a notice list.</span>
                            </div>
                        </label>
                        <label class="relative flex items-center p-4 bg-white border border-slate-200 rounded-xl cursor-pointer hover:border-indigo-300 transition-colors">
                            <input type="radio" name="hero_type" value="overlay" {{ ($options['institute.hero.type'] ?? '') == 'overlay' ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <div class="ml-4">
                                <span class="block text-sm font-bold text-slate-900">Overlay / Netflix</span>
                                <span class="block text-xs text-slate-500">Modern cinematic full-screen look.</span>
                            </div>
                        </label>
                    </div>
                </div>
                </div>

                <!-- Existing Sliders Section -->
                <div class="space-y-6">
                    <h2 class="text-lg font-bold text-slate-900 mb-4 border-b border-slate-100 pb-2">Currently Active Sliders</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($sliders as $index => $item)
                        <div class="relative group bg-slate-50 border border-slate-200 rounded-xl overflow-hidden p-4">
                            <input type="hidden" name="existing_sliders[{{ $index }}][url]" value="{{ $item['url'] }}">
                            <input type="hidden" name="existing_sliders[{{ $index }}][path]" value="{{ $item['path'] }}">
                            
                            <img src="{{ $item['url'] }}" class="w-full h-32 object-cover rounded-lg mb-4 opacity-50 group-hover:opacity-100 transition-opacity">
                            
                            <div class="space-y-2">
                                <template x-if="true">
                                    <label class="flex items-center text-sm font-bold text-red-500 cursor-pointer mb-2 border border-red-200 bg-red-50 px-2 py-1 rounded">
                                        <input type="checkbox" name="existing_sliders[{{ $index }}][delete]" value="1" class="rounded text-red-500 mr-2 focus:ring-red-400">
                                        Remove from Slider
                                    </label>
                                </template>
                                
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Update Title</label>
                                    <input type="text" name="existing_sliders[{{ $index }}][title]" value="{{ $item['title'] ?? '' }}" 
                                           class="w-full border-slate-200 rounded-lg text-xs font-semibold focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Display Order</label>
                                    <input type="number" name="existing_sliders[{{ $index }}][order]" value="{{ $item['order'] ?? 0 }}" 
                                           class="w-full border-slate-200 rounded-lg text-xs font-semibold focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Replace Image</label>
                                    <input type="file" name="existing_sliders[{{ $index }}][image]" class="w-full text-[10px] text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full border-2 border-dashed border-slate-200 py-12 rounded-2xl text-center">
                            <i class="fas fa-images text-slate-200 text-6xl mb-4"></i>
                            <p class="text-slate-400 text-sm font-bold uppercase tracking-widest">No Slides Found</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- New Sliders Section -->
                <div class="mt-12 pt-12 border-t-2 border-slate-100">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-lg font-black text-slate-900">Add New Slides</h2>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Recommended size: 1920 x 800 (9:1 aspect ratio)</p>
                        </div>
                        <button type="button" @click="addSlider()" 
                                class="inline-flex items-center px-6 py-2 bg-emerald-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-emerald-100 hover:bg-emerald-700 transition transform hover:-translate-y-1">
                            <i class="fas fa-plus-circle mr-2"></i> Add Slot
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(slider, index) in newSliders" :key="index">
                            <div class="p-6 bg-slate-50 border border-slate-200 rounded-2xl flex flex-col md:flex-row gap-6">
                                <div class="flex-1 space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label class="text-xs font-black text-slate-700 uppercase tracking-widest">Slide Title</label>
                                            <input type="text" :name="`new_slider_titles[${index}]`" x-model="slider.title" placeholder="Caption overlay text"
                                                   class="w-full border-slate-200 rounded-xl text-sm font-bold focus:ring-emerald-500 focus:border-emerald-500">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-xs font-black text-slate-700 uppercase tracking-widest">Sort Order</label>
                                            <input type="number" :name="`new_slider_orders[${index}]`" x-model="slider.order"
                                                   class="w-full border-slate-200 rounded-xl text-sm font-bold focus:ring-emerald-500 focus:border-emerald-500">
                                        </div>
                                    </div>
                                    <input type="file" name="new_sliders[]" required class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                                </div>
                                <button type="button" @click="removeNew(index)" class="self-start text-red-500 hover:text-red-700 font-bold text-sm px-4 py-2 hover:bg-red-50 rounded-xl transition">
                                    Cancel
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
