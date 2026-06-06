@if(isset($notices) && count($notices) > 0)
    <div class="relative z-40 my-4">
        <div class="max-w-[90%] mx-auto px-0 md:px-6 lg:px-8 flex items-center font-bn">
            <div class="bg-linear-to-r from-rose-600 to-red-600 text-white px-5 py-1.5 font-bold text-sm whitespace-nowrap rounded-l-lg shadow-md relative z-10 hidden sm:block">
                নোটিশ:
                <div class="absolute right-0 top-0 h-full w-3 bg-red-800 skew-x-12 transform translate-x-1.5"></div>
            </div>
            <div class="bg-red-600 text-white px-3 py-1 font-bold text-xs whitespace-nowrap rounded-l-md block sm:hidden relative z-10">
                নোটিশ:
            </div>
            <div class="flex-1 overflow-hidden ml-0 sm:-ml-2 bg-indigo-900/50 rounded-r-md border border-indigo-500/30 shadow-inner py-1.5 px-4 h-full flex items-center relative z-0">
                <marquee direction="left" onmouseover="this.stop();" onmouseout="this.start();" class="text-indigo-100 text-sm md:text-base align-middle font-medium tracking-wide w-full" scrollamount="5">
                    @foreach($notices as $notice)
                        @if($notice->is_urgent)
                            <span class="bg-yellow-400 text-indigo-950 text-[10px] px-1.5 py-0.5 rounded font-black mr-2 animate-pulse">URGENT</span>
                        @endif
                        <a href="{{ route('notices.show', $notice->id) }}" class="hover:text-yellow-400 transition-colors mx-4">{{ $notice->title }}</a>
                        @if(!$loop->last)
                            <span class="text-indigo-500/80">|</span>
                        @endif
                    @endforeach
                </marquee>
            </div>
        </div>
    </div>
@endif
