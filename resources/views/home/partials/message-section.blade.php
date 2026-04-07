@php
    function getRowItemClass(string $config): string {
        return match ($config) {
            '1 item' => 'md:col-span-6',
            '2 items' => 'md:col-span-3',
            default => 'md:col-span-2',
        };
    }

    function getImageUrl($speech) {
        if (is_array($speech)) {
            return $speech['image_json']['url'] ?? '/images/teacher.png';
        }
        return $speech->image_json['url'] ?? '/images/teacher.png';
    }
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 font-bn">
    <!-- About Us -->
    <div class="bg-white rounded-2xl shadow-xl p-8 lg:p-12 mb-12 transform hover:shadow-2xl transition duration-500">
        <div class="flex flex-col lg:flex-row gap-12 items-center">
            <div class="w-full lg:w-1/3">
                <div class="relative">
                    <div class="absolute -inset-2 bg-indigo-100 rounded-full blur-xl opacity-50"></div>
                    <img src="{{ $options['institute.branding.logo_json']['url'] ?? '/images/school-logo.png' }}" class="relative w-64 h-64 mx-auto object-contain rounded-full border-8 border-white shadow-lg" alt="About">
                </div>
            </div>
            <div class="w-full lg:w-2/3">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-1 bg-indigo-600 rounded"></div>
                    <h2 class="text-3xl font-extrabold text-indigo-900">{{ $options['institute.about.title'] ?? 'আমাদের প্রতিষ্ঠান সম্পর্কে' }}</h2>
                </div>
                <p class="text-gray-600 leading-relaxed text-lg mb-8">
                    {{ $options['institute.about.text'] ?? 'আমাদের শিক্ষা প্রতিষ্ঠান একটি ঐতিহ্যবাহী বিদ্যাপীঠ। দীর্ঘ পথচলায় আমরা অসংখ্য মেধাবী শিক্ষার্থী উপহার দিয়েছি যারা দেশ ও দশের কল্যাণে নিয়োজিত।' }}
                </p>
                <a href="/about-us" class="inline-flex items-center bg-indigo-600 text-white px-8 py-3 rounded-full font-bold hover:bg-indigo-700 transition shadow-lg hover:shadow-indigo-200">
                    {{ $options['institute.about.button_text'] ?? 'আরও পড়ুন' }}
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Dynamic Speech Section (row/column placement driven by row config) -->
    @php
        $rows = [1, 2, 3];
    @endphp
    <div class="space-y-8 mb-16">
        @foreach($rows as $rowIndex)
            @php
                $rowConfig = $options["speech.row.{$rowIndex}.config"] ?? '1 item';
                $maxItems = $rowConfig === '1 item' ? 1 : ($rowConfig === '2 items' ? 2 : 3);
                $rowItems = collect($speeches)
                    ->where('row_index', $rowIndex)
                    ->sortBy('column_index')
                    ->take($maxItems);
            @endphp

            @if($rowItems->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-6 gap-8">
                    @foreach($rowItems as $speech)
                        <div class="bg-white rounded-2xl shadow-xl overflow-hidden group flex flex-col {{ getRowItemClass($rowConfig) }}">
                            <div class="p-8 flex-1 flex flex-col">
                                <div class="flex flex-col items-center mb-6">
                                    <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-indigo-50 shadow-inner mb-4">
                                        <img src="{{ getImageUrl($speech) }}"
                                             class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition duration-500"
                                             alt="{{ $speech['name'] ?? $speech->name }}">
                                    </div>
                                    <h3 class="text-xl font-bold text-indigo-900 text-center">{{ $speech['title'] ?? $speech->title }}</h3>
                                </div>
                                <div class="relative flex-1">
                                    <svg class="absolute -top-4 -left-2 w-10 h-10 text-indigo-50 opacity-50" fill="currentColor" viewBox="0 0 32 32">
                                        <path d="M10 12c-2.209 0-4 1.791-4 4s1.791 4 4 4c0.75 0 1.444-0.213 2.031-0.563 1.156 2.406 3.444 4.063 6.094 4.313a1.001 1.001 0 100.188-1.969c-3.125-0.281-5.656-2.5-6.188-5.594C12.313 15.688 11.25 12 10 12zm12 0c-2.209 0-4 1.791-4 4s1.791 4 4 4c0.75 0 1.444-0.213 2.031-0.563 1.156 2.406 3.444 4.063 6.094 4.313a1.001 1.001 0 100.188-1.969c-3.125-0.281-5.656-2.5-6.188-5.594C24.313 15.688 23.25 12 22 12z"></path>
                                    </svg>
                                    <p class="text-gray-600 italic text-center px-4 relative z-10 line-clamp-6 group-hover:line-clamp-none transition-all duration-500">
                                        {{ $speech['speech'] ?? $speech->speech }}
                                    </p>
                                </div>
                                <div class="mt-6 text-right pt-4 border-t border-gray-50">
                                    <div class="font-bold text-indigo-900">{{ $speech['name'] ?? $speech->name }}</div>
                                    <div class="text-xs text-indigo-600 font-medium tracking-wide uppercase">{{ $speech['designation'] ?? $speech->designation }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>
</div>
