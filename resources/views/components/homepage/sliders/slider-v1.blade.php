@props([
    'slides'  => [],
    'notices' => [],
    'isSliderOnly' => false,
])

@php
    $interval = 7000;
@endphp

<div class="relative overflow-hidden group font-sans rounded-md max-w-[90%] md:max-w-[86%] mx-auto {{ $isSliderOnly ? 'w-full h-[30vh] md:h-[65vh]' : 'lg:w-2/3 h-64 md:h-80 lg:h-96 rounded-2xl shadow-2xl' }}"
     x-data="{ currentSlide: 0, totalSlides: {{ count($slides) }}, next() { this.currentSlide = (this.currentSlide + 1) % this.totalSlides }, prev() { this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides } }"
     x-init="setInterval(() => next(), {{ $interval }})">

    @foreach($slides as $index => $slide)
        <div class="absolute inset-0 transition-opacity duration-1500 ease-in-out"
             :class="currentSlide === {{ $index }} ? 'opacity-100 z-10' : 'opacity-0 z-0'">
            <img src="{{ is_array($slide) ? $slide['url'] : $slide->url }}"
                 class="w-full h-full object-cover transform duration-10000 ease-linear"
                 :class="currentSlide === {{ $index }} ? 'scale-110' : 'scale-100'"
                 alt="Slide">

            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-transparent to-transparent"></div>

            <div class="absolute bottom-0 left-0 w-full p-6 {{ $isSliderOnly ? 'md:p-16 lg:p-24' : 'md:p-8 lg:p-12' }} z-20"
                 x-show="currentSlide === {{ $index }}"
                 x-transition:enter="transition ease-out duration-700 delay-300"
                 x-transition:enter-start="opacity-0 translate-y-10"
                 x-transition:enter-end="opacity-100 translate-y-0">
                <div class="max-w-4xl">
                    @if(isset($slide['published_at']))
                        <div class="flex items-center gap-3 mb-2 md:mb-4">
                            <span class="bg-indigo-600 text-white text-[10px] md:text-xs font-bold px-2 md:px-3 py-0.5 md:py-1 rounded-sm uppercase tracking-widest bg-opacity-90">
                                {{ \Carbon\Carbon::parse($slide['published_at'])->format('d M Y') }}
                            </span>
                            <span class="w-1 h-1 bg-gray-400 rounded-full"></span>
                            <span class="text-gray-300 text-xs md:text-sm font-medium tracking-wide">Featured</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach

    <div class="absolute bottom-8 right-8 {{ $isSliderOnly ? 'lg:right-24' : '' }} z-30 flex gap-2">
        @foreach($slides as $index => $slide)
            <div @click="currentSlide = {{ $index }}"
                 class="h-1.5 {{ $isSliderOnly ? 'w-12 md:w-20' : 'w-10' }} bg-gray-600/50 cursor-pointer overflow-hidden rounded-full">
                <div class="h-full bg-white transition-all duration-7000 ease-linear"
                     :style="currentSlide === {{ $index }} ? 'width: 100%' : 'width: 0%'"></div>
            </div>
        @endforeach
    </div>
</div>
