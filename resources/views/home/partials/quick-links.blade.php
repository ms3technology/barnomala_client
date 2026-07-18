<div class="bg-linear-to-b from-gray-50 to-white py-20 reveal">
    <div class="max-w-[90%] mx-auto px-0 md:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-16">
            <!-- Quick Access Grid -->
            <div class="lg:col-span-2">
                <div class="mb-12">
                    <h2 class="text-4xl font-black text-gray-900 mb-2">
                        Quick Access
                    </h2>
                    <p class="text-gray-500 font-medium">Find what you need in seconds</p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
                    @foreach($quickLinks as $link)
                        <a href="{{ $link['url'] }}" 
                           class="group flex flex-col items-center justify-center p-6 rounded-2xl border border-gray-100 transition-all duration-300 hover:border-indigo-300 hover:shadow-lg bg-indigo-50">
                            
                            <!-- Icon Container -->
                            <div class="w-12 h-12 mb-4 rounded-full flex items-center justify-center transition-all duration-300 bg-indigo-600">
                                <i class="fa fa-{{ $link['icon'] }} text-xl text-white transition-colors"></i>
                            </div>

                            <!-- Label -->
                            <span class="font-bold text-sm text-center transition-colors text-indigo-600">
                                {{ $link['label'] }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Important Links Sidebar -->
            <div class="h-min lg:col-span-1 bg-amber-200 rounded-2xl p-6 border border-gray-200">
                <h2 class="text-2xl font-black text-gray-900 mb-8 font-bn">গুরুত্বপূর্ণ লিঙ্ক</h2>

                <div class="space-y-3">
                    @foreach($importantLinks as $link)
                        <a href="{{ $link['url'] ?? $link->url }}" target="_blank"
                           class="flex items-center gap-3 p-4 rounded-xl bg-white border border-gray-200 text-gray-700 font-semibold transition-all duration-300 hover:border-indigo-400 hover:bg-indigo-50 hover:text-indigo-600 group">
                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center shrink-0 transition-all group-hover:bg-indigo-100">
                                <svg class="w-4 h-4 text-gray-600 transition-colors group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.172 13.828a4 4 0 005.656 0l4-4a4 4 0 10-5.656-5.656l-1.102 1.101"></path>
                                </svg>
                            </div>
                            <span class="text-sm flex-1">{{ $link['title'] ?? $link->title }}</span>
                            <svg class="w-4 h-4 opacity-0 -translate-x-2 transition-all group-hover:opacity-100 group-hover:translate-x-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
