@php
    $heroType = $theme->currentValue('slider_type');
    $isSliderOnly = $heroType === 'slider_only';
@endphp

<div class="mt-8 mb-3 {{ $isSliderOnly ? 'w-full' : 'max-w-[90%] mx-auto px-0 md:px-6 lg:px-8' }}">
    <div class="flex flex-col lg:flex-row {{ $isSliderOnly ? '' : 'gap-8' }}">

        {{-- Slider design is resolved at runtime by ThemeService --}}
        <x-dynamic-component
            :component="$theme->component('slider')"
            :slides="$sliderImages"
            :notices="$notices"
            :is-slider-only="$isSliderOnly" />

        {{-- Latest News Side Panel --}}
        @if(!$isSliderOnly)
            <div class="lg:w-1/3 flex flex-col group h-64 md:h-80 lg:h-96 font-bn">
                <div class="bg-accent text-white p-5 rounded-t-2xl font-bold flex justify-between items-center shadow-lg relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-white/5 rounded-full blur-2xl -mr-10 -mt-10"></div>
                    <span class="text-xl flex items-center gap-3 relative z-10 tracking-wide">
                        <span class="w-1.5 h-6 bg-yellow-400 rounded-full inline-block shadow-[0_0_10px_rgba(250,204,21,0.5)]"></span>
                        ভর্তি ও নোটিশ
                    </span>
                    <svg class="w-6 h-6 text-indigo-300 relative z-10 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                    <a href="{{ route('notices.index') }}" class="relative z-10 text-sm font-bold text-white underline-offset-4 hover:underline transition-all">সব দেখুন</a>
                </div>
                <div class="flex-1 overflow-hidden">
                    <div class="p-2 space-y-3 overflow-y-auto max-h-96 scrollbar-thin scrollbar-thumb-indigo-100 scrollbar-track-transparent">
                        @foreach($notices as $notice)
                            <div class="group/item border border-gray-100 bg-white rounded-xl px-4 py-3 hover:bg-indigo-50/80 hover:border-indigo-100 transition-all duration-300">
                                <a href="{{ route('notices.show', $notice->id) }}" class="flex gap-4 items-center">
                                    <div class="bg-white text-indigo-700 w-26 h-8 text-sm rounded-xl shrink-0 flex items-center justify-center font-bold shadow-sm border border-indigo-50 transition-all duration-300 transform group-hover/item:-translate-y-1">
                                        <span class="text-base leading-none">{{ formatDateBN($notice->published_at, 'day') }} - {{ formatDateBN($notice->published_at, 'month') }}</span>
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
