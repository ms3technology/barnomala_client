@php
    $heroType = $options['institute.hero.type'] ?? 'slider_with_notice';
    $sliderDesign = $options['institute.hero.type'] === 'overlay' ? 'overlay' : ($options['institute.hero.slider_design'] ?? 'slider_1');
    $isSliderOnly = $heroType === 'slider_only' || $heroType === 'overlay';

    function formatDateBN($dateString, $type = 'day') {
        $date = new \DateTime($dateString);
        if ($type === 'day') {
            $days = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
            return str_replace(['0','1','2','3','4','5','6','7','8','9'], $days, $date->format('d'));
        }
        $months = ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'];
        return $months[intval($date->format('m')) - 1];
    }
@endphp

<div class="{{ $sliderDesign === 'overlay' ? 'w-full mb-16' : 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 mb-16' }}">
    <div class="flex flex-col lg:flex-row {{ $sliderDesign === 'overlay' ? '' : 'gap-8' }}">
        
        <!-- Slider -->
        @if($sliderDesign === 'overlay')
            <div class="relative w-full mt-4 h-[60vh] md:h-[75vh] lg:h-[85vh] overflow-hidden group font-sans" 
                 x-data="{ currentSlide: 0, totalSlides: {{ count($sliderImages) }}, next() { this.currentSlide = (this.currentSlide + 1) % this.totalSlides }, prev() { this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides } }" 
                 x-init="setInterval(() => next(), 7000)">
                
                @foreach($sliderImages as $index => $slide)
                    <div class="absolute inset-0 transition-opacity duration-1500 ease-in-out"
                         :class="currentSlide === {{ $index }} ? 'opacity-100 z-10' : 'opacity-0 z-0'">
                        <img src="{{ is_array($slide) ? $slide['url'] : $slide->url }}" 
                             class="w-full h-full object-cover transform duration-10000 ease-linear"
                             :class="currentSlide === {{ $index }} ? 'scale-110' : 'scale-100'"
                             alt="Slide">
                        
                        <!-- Netflix style gradients -->
                        <div class="absolute inset-0 bg-linear-to-t from-black via-black/20 to-transparent"></div>
                        <div class="absolute inset-0 bg-linear-to-r from-black/60 via-transparent to-transparent"></div>

                        <!-- Text Content -->
                        <div class="absolute bottom-0 left-0 w-full p-6 md:p-16 lg:p-24 z-20" 
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

                                <h2 class="text-2xl md:text-5xl lg:text-7xl font-black text-white mb-4 md:mb-6 leading-tight drop-shadow-2xl">
                                    {{ $slide['title'] ?? 'Institute Update' }}
                                </h2>

                                @if(isset($slide['description']))
                                    <p class="text-sm md:text-xl text-gray-200 line-clamp-2 md:line-clamp-3 max-w-2xl font-medium mb-6 md:mb-8 drop-shadow-md leading-relaxed">
                                        {{ $slide['description'] }}
                                    </p>
                                @endif

                                <div class="flex items-center gap-3 md:gap-4">
                                    <button class="bg-white text-black px-4 md:px-8 py-2 md:py-3 rounded-md font-bold text-sm md:text-lg flex items-center gap-2 hover:bg-white/90 transition-all transform hover:scale-105">
                                        <svg class="w-4 h-4 md:w-6 md:h-6 fill-current" viewBox="0 0 24 24"><path d="M7 6v12l10-6z"/></svg>
                                        <span>Read More</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Progressive Dots (Netflix Bottom Bar style) -->
                <div class="absolute bottom-8 right-8 lg:right-24 z-30 flex gap-2">
                    @foreach($sliderImages as $index => $slide)
                        <div @click="currentSlide = {{ $index }}"
                             class="h-1.5 w-12 md:w-20 bg-gray-600/50 cursor-pointer overflow-hidden rounded-full">
                            <div class="h-full bg-white transition-all duration-7000 ease-linear"
                                 :style="currentSlide === {{ $index }} ? 'width: 100%' : 'width: 0%'"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        @elseif($sliderDesign === 'slider_1')
        <div class="relative group rounded-2xl overflow-hidden shadow-[0_20px_50px_rgba(8,112,184,0.1)] border border-gray-100 transform hover:-translate-y-1 transition-transform duration-500 h-64 md:h-80 lg:h-96 {{ $isSliderOnly ? 'w-full' : 'lg:w-2/3' }}" x-data="{ currentSlide: 0, totalSlides: {{ count($sliderImages) }}, next() { this.currentSlide = (this.currentSlide + 1) % this.totalSlides }, prev() { this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides } }" x-init="setInterval(() => next(), 5000)">
            @foreach($sliderImages as $index => $slide)
                <div class="absolute inset-0 transition-opacity duration-1000 ease-in-out"
                     :class="currentSlide === {{ $index }} ? 'opacity-100' : 'opacity-0 z-0'">
                    <img src="{{ is_array($slide) ? $slide['url'] : $slide->url }}" class="w-full h-full object-cover" alt="Slide">
                    <div class="absolute inset-0 bg-linear-to-b from-transparent via-transparent to-black/50"></div>
                </div>
            @endforeach
            
            <!-- Controls -->
            <button @click="prev()" class="absolute left-6 top-1/2 -translate-y-1/2 bg-white/20 backdrop-blur-md hover:bg-white text-white hover:text-indigo-900 p-3 rounded-full transition-all duration-300 opacity-0 group-hover:opacity-100 z-10 shadow-lg border border-white/30 transform hover:scale-110">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <button @click="next()" class="absolute right-6 top-1/2 -translate-y-1/2 bg-white/20 backdrop-blur-md hover:bg-white text-white hover:text-indigo-900 p-3 rounded-full transition-all duration-300 opacity-0 group-hover:opacity-100 z-10 shadow-lg border border-white/30 transform hover:scale-110">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
            
            <!-- Dots -->
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex space-x-3 z-10 bg-black/20 backdrop-blur-md px-4 py-2 rounded-full border border-white/10">
                @foreach($sliderImages as $index => $slide)
                    <div @click="currentSlide = {{ $index }}"
                         class="w-2.5 h-2.5 rounded-full cursor-pointer transition-all duration-300"
                         :class="currentSlide === {{ $index }} ? 'bg-yellow-400 w-8 shadow-[0_0_10px_rgba(250,204,21,0.8)]' : 'bg-white/60 hover:bg-white'"></div>
                @endforeach
            </div>
        </div>
        @else
        <div class="relative group rounded-3xl overflow-hidden shadow-2xl transition-all duration-700 h-64 md:h-80 lg:h-96 {{ $isSliderOnly ? 'w-full' : 'lg:w-2/3' }}" x-data="{ currentSlide: 0, totalSlides: {{ count($sliderImages) }}, next() { this.currentSlide = (this.currentSlide + 1) % this.totalSlides }, prev() { this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides } }" x-init="setInterval(() => next(), 6000)">
            @foreach($sliderImages as $index => $slide)
                <div class="absolute inset-0 transition-all duration-1000 ease-in-out transform"
                     :class="currentSlide === {{ $index }} ? 'opacity-100 scale-100' : 'opacity-0 scale-110 z-0'">
                    <img src="{{ is_array($slide) ? $slide['url'] : $slide->url }}" class="w-full h-full object-cover" alt="Slide">
                    <div class="absolute inset-0 bg-linear-to-r from-black/60 via-transparent to-black/60"></div>
                    
                    @if(isset($slide['title']) || isset($slide['description']))
                    <div class="absolute inset-0 flex flex-col justify-end p-8 md:p-12 text-white" x-show="currentSlide === {{ $index }}" x-transition:enter="transition ease-out duration-500 delay-300" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0">
                        @if(isset($slide['title']))<h2 class="text-2xl md:text-4xl font-bold mb-2">{{ $slide['title'] }}</h2>@endif
                        @if(isset($slide['description']))<p class="text-sm md:text-lg text-gray-200 line-clamp-2 max-w-2xl">{{ $slide['description'] }}</p>@endif
                    </div>
                    @endif
                </div>
            @endforeach
            
            <!-- Minimal Controls -->
            <div class="absolute inset-y-0 left-4 flex items-center">
                <button @click="prev()" class="w-10 h-10 rounded-full bg-black/30 backdrop-blur-sm text-white flex items-center justify-center hover:bg-indigo-600 transition-colors duration-300 border border-white/20 transform hover:scale-110">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
            </div>
            <div class="absolute inset-y-0 right-4 flex items-center">
                <button @click="next()" class="w-10 h-10 rounded-full bg-black/30 backdrop-blur-sm text-white flex items-center justify-center hover:bg-indigo-600 transition-colors duration-300 border border-white/20 transform hover:scale-110">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
            
            <!-- Progress Bar Style Dots -->
            <div class="absolute bottom-6 left-8 right-8 flex gap-2 z-10">
                @foreach($sliderImages as $index => $slide)
                    <div @click="currentSlide = {{ $index }}"
                         class="h-1 flex-1 rounded-full cursor-pointer transition-all duration-300 bg-white/30 overflow-hidden relative">
                         <div class="absolute inset-y-0 left-0 bg-indigo-500 transition-all duration-6000 ease-linear" :style="currentSlide === {{ $index }} ? 'width: 100%' : 'width: 0%'"></div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Latest News Side Panel -->
        @if(!$isSliderOnly)
            <div class="lg:w-1/3 flex flex-col group h-64 md:h-80 lg:h-96 font-bn">
                <div class="bg-accent text-white p-5 rounded-t-2xl font-bold flex justify-between items-center shadow-lg relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-white/5 rounded-full blur-2xl -mr-10 -mt-10"></div>
                    <span class="text-xl flex items-center gap-3 relative z-10 tracking-wide">
                        <span class="w-1.5 h-6 bg-yellow-400 rounded-full inline-block shadow-[0_0_10px_rgba(250,204,21,0.5)]"></span>
                        ভর্তি ও নোটিশ
                    </span>
                    <svg class="w-6 h-6 text-indigo-300 relative z-10 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                </div>
                <div class="bg-white border-x border-b border-gray-100 rounded-b-2xl flex-1 overflow-hidden shadow-[0_10px_30px_rgba(8,112,184,0.05)] hover:shadow-[0_15px_40px_rgba(8,112,184,0.08)] transition-all">
                    <div class="p-4 space-y-3 overflow-y-auto h-full scrollbar-thin scrollbar-thumb-indigo-100 scrollbar-track-transparent">
                        @foreach($notices as $notice)
                            <div class="group/item border border-gray-50 bg-gray-50/50 rounded-xl p-4 hover:bg-indigo-50/80 hover:border-indigo-100 transition-all duration-300">
                                <a href="{{ route('notices.show', $notice->id) }}" class="flex gap-4 items-start">
                                    <div class="bg-white text-indigo-700 w-13 h-13 rounded-xl shrink-0 flex flex-col items-center justify-center font-bold shadow-sm border border-indigo-50 transition-all duration-300 transform group-hover/item:-translate-y-1">
                                        <span class="text-base leading-none">{{ formatDateBN($notice->published_at, 'day') }}</span>
                                        <span class="text-[10px] uppercase font-bold tracking-wider mt-1 opacity-80">{{ formatDateBN($notice->published_at, 'month') }}</span>
                                    </div>
                                    <div class="flex-1">
                                        @if($notice->is_urgent)
                                            <div class="text-[10px] font-black text-rose-600 mb-1 flex items-center gap-1">
                                                <span class="relative flex h-2 w-2">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                                                </span>
                                                জরুরি
                                            </div>
                                        @endif
                                        <h4 class="text-gray-800 font-bold text-sm leading-snug line-clamp-2 group-hover/item:text-indigo-700 transition-colors">{{ $notice->title }}</h4>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
