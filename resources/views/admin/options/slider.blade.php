@extends('layouts.admin')

@section('title', 'Manage Sliders')

@push('header_actions')
    <button type="submit" form="slider-form"
        class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
        <i class="fas fa-save mr-2"></i>
        Save Sliders
    </button>
@endpush

@section('content')
    <div x-data="{
        newSliders: [],
        existingPreviews: {},
        addSlider() {
            this.newSliders.push({
                title: '',
                order: (this.newSliders.length + {{ count($sliders) }}),
                preview: null
            });
        },
        removeNew(index) {
            this.newSliders.splice(index, 1);
        },
        handleFileChange(event, index, isNew = false) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                if (isNew) {
                    this.newSliders[index].preview = e.target.result;
                } else {
                    this.existingPreviews[index] = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        }
    }">
        <form id="slider-form" action="{{ route('admin.sliders.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Grid Container -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">

                <!-- Existing Sliders -->
                @foreach ($sliders as $index => $item)
                    <div class="group relative bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden transition-all duration-300 hover:shadow-lg hover:border-indigo-300"
                        x-data="{ isDeleted: false }" x-show="!isDeleted">

                        <!-- Image Preview Area -->
                        <div class="relative aspect-video bg-slate-100 overflow-hidden">
                            <img :src="existingPreviews[{{ $index }}] || '{{ $item['url'] }}'"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">

                            <!-- Quick Actions Overlay -->
                            <div
                                class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                                <label
                                    class="cursor-pointer bg-white/90 hover:bg-white text-slate-900 w-10 h-10 rounded-full flex items-center justify-center shadow-lg transition-transform hover:scale-110">
                                    <i class="fas fa-camera text-sm"></i>
                                    <input type="file" name="existing_sliders[{{ $index }}][image]"
                                        @change="handleFileChange($event, {{ $index }})" class="hidden">
                                </label>
                                <button type="button" @click="isDeleted = true"
                                    class="bg-red-500/90 hover:bg-red-600 text-white w-10 h-10 rounded-full flex items-center justify-center shadow-lg transition-transform hover:scale-110">
                                    <i class="fas fa-trash-alt text-sm"></i>
                                </button>
                            </div>

                            <div class="absolute top-3 left-3">
                                <span
                                    class="bg-black/50 backdrop-blur-md text-white px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                                    Slide #{{ $index + 1 }}
                                </span>
                            </div>
                        </div>

                        <!-- Form Content -->
                        <div class="p-4 space-y-3">
                            <input type="hidden" name="existing_sliders[{{ $index }}][delete]"
                                :value="isDeleted ? '1' : '0'">
                            <input type="hidden" name="existing_sliders[{{ $index }}][url]"
                                value="{{ $item['url'] }}">
                            <input type="hidden" name="existing_sliders[{{ $index }}][path]"
                                value="{{ $item['path'] }}">

                            <div>
                                <input type="text" name="existing_sliders[{{ $index }}][title]"
                                    value="{{ $item['title'] ?? '' }}" placeholder="Enter headline..."
                                    class="w-full text-sm font-bold border-b-2 border-slate-500 focus:outline-none focus:border-indigo-500 placeholder:text-slate-500 transition-all">
                            </div>

                            <div class="flex items-center gap-2">
                                <div class="px-2 py-1 text-[10px] font-bold text-slate-500 uppercase tracking-tighter">
                                    Display Order
                                </div>
                                <input type="text" name="existing_sliders[{{ $index }}][order]"
                                    value="{{ $item['order'] ?? 0 }}"
                                    class="w-full border-none bg-zinc-200 text-xs font-black text-slate-800 focus:ring-0 p-2">
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- New Sliders (Alpine Template) -->
                <template x-for="(slider, index) in newSliders" :key="index">
                    <div class="group relative bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden transition-all hover:shadow-lg hover:border-indigo-300 animate-in fade-in slide-in-from-bottom-4 duration-500">

                        <!-- Image Preview / Upload Button -->
                        <div class="relative aspect-video bg-slate-100 flex flex-col items-center justify-center overflow-hidden">
                            <template x-if="slider.preview">
                                <img :src="slider.preview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!slider.preview">
                                <div class="text-center p-4">
                                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-2 shadow-sm border border-slate-200">
                                        <i class="fas fa-cloud-upload-alt text-slate-400 text-lg"></i>
                                    </div>
                                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">No Image Selected</p>
                                </div>
                            </template>

                            <!-- Actions for New Slide -->
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                                <label class="cursor-pointer bg-white/90 hover:bg-white text-slate-900 w-10 h-10 rounded-full flex items-center justify-center shadow-lg transition-transform hover:scale-110">
                                    <i class="fas fa-camera text-sm"></i>
                                    <input type="file" name="new_sliders[]" required @change="handleFileChange($event, index, true)" class="hidden">
                                </label>
                                <button type="button" @click="removeNew(index)"
                                    class="bg-red-500/90 hover:bg-red-600 text-white w-10 h-10 rounded-full flex items-center justify-center shadow-lg transition-transform hover:scale-110">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </div>

                            <div class="absolute top-3 left-3">
                                <span class="bg-indigo-600 text-white px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider shadow-sm">
                                    New Slide
                                </span>
                            </div>
                        </div>

                        <!-- Form Content -->
                        <div class="p-4 space-y-3">
                            <div>
                                <input type="text" :name="`new_slider_titles[${index}]`" x-model="slider.title"
                                    placeholder="Enter headline..."
                                    class="w-full text-sm font-bold border-b-2 border-slate-500 focus:outline-none focus:border-indigo-500 placeholder:text-slate-500 transition-all">
                            </div>

                            <div class="flex items-center gap-2">
                                <div class="px-2 py-1 text-[10px] font-bold text-slate-500 uppercase tracking-tighter">
                                    Display Order
                                </div>
                                <input type="number" :name="`new_slider_orders[${index}]`" x-model="slider.order"
                                    class="w-full border-none bg-zinc-200 text-xs font-black text-slate-800 focus:ring-0 p-2">
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Add New Slide Action Card -->
                <button type="button" @click="addSlider()"
                    class="group relative aspect-4/3 md:aspect-auto flex flex-col items-center justify-center bg-slate-50 transition-all duration-300 hover:bg-indigo-50 hover:border-indigo-300 hover:shadow-md active:scale-[0.98]">
                    <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-100 group-hover:border-indigo-100 group-hover:scale-110 transition-transform mb-3">
                        <i class="fas fa-plus text-slate-400 group-hover:text-indigo-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-black text-slate-500 group-hover:text-indigo-600 uppercase tracking-widest">Add New Slide</span>
                </button>

                <!-- Empty State -->
                @if (count($sliders) == 0)
                    <div x-show="newSliders.length == 0"
                        class="col-span-full py-20 bg-slate-50/50 rounded-3xl border-4 border-dashed border-slate-100 flex flex-col items-center justify-center text-center">
                        <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-sm mb-4">
                            <i class="fas fa-image text-slate-300 text-3xl"></i>
                        </div>
                        <h3 class="text-slate-900 font-bold">No slides found</h3>
                        <p class="text-slate-500 text-sm mt-1 max-w-xs">Start building your homepage showcase by adding your
                            first slide.</p>
                        <button type="button" @click="addSlider()"
                            class="mt-6 text-indigo-600 font-bold text-sm hover:underline">
                            Create the first slide now →
                        </button>
                    </div>
                @endif

            </div>
        </form>
    </div>
@endsection
