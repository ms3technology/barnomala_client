<div class="bg-indigo-900 py-12 text-white overflow-hidden relative reveal">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-64 h-64 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-1/3 translate-y-1/3"></div>
    </div>
    <div class="max-w-[90%] mx-auto px-0 md:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            @foreach($stats as $stat)
                <div class="text-center group">
                    <div class="text-4xl font-extrabold mb-1 tracking-tight">
                        <span class="counter-up" data-count="{{ (int)$stat['count'] }}">0</span>+
                    </div>
                    <div class="text-gray-300 font-medium uppercase tracking-wider text-xs">{{ $stat['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>
