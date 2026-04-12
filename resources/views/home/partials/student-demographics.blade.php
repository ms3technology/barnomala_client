<section class="py-12 bg-white reveal">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Left Side: Class-wise Grid (w-2/3) --}}
            <div class="w-full lg:w-2/3">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 uppercase tracking-wider">Student Demographics</h2>
                    <div class="mt-1 w-20 h-1 bg-indigo-600"></div>
                </div>

                @php
                    $classes = collect($options['institute.demographics.classes'] ?? [])
                        ->filter(fn($count) => $count > 10)
                        ->all();
                @endphp
                <div class="grid grid-cols-2 sm:grid-cols-3 {{ count($classes) > 8 ? 'md:grid-cols-5' : 'md:grid-cols-4' }} gap-6">
                    @forelse($classes as $class => $count)
                        <div class="flex flex-col items-center group">
                            <div class="w-20 h-20 md:w-24 md:h-24 rounded-full flex items-center justify-center bg-indigo-50 mb-3 transition-all duration-300 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:shadow-[0_0_30px_rgba(79,70,229,0.3)]">
                                <span class="text-xl md:text-2xl font-bold text-indigo-700 group-hover:text-white counter-up" data-count="{{ $count }}">0</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-600 uppercase transition-colors group-hover:text-indigo-600">{{ $class }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 italic">No class data available</p>
                    @endforelse
                </div>
            </div>

            {{-- Right Side: Summary List (w-1/3) --}}
            <div class="w-full lg:w-1/3 bg-gray-100 p-8 rounded-2xl border border-gray-100">
                <h3 class="text-xl font-bold text-gray-800 mb-6 uppercase border-b pb-4">Summary</h3>
                
                @php
                    $genderData = $options['institute.demographics.gender'] ?? [];
                    $religionData = $options['institute.demographics.religion'] ?? [];
                    $totalStudents = array_sum($options['institute.demographics.classes'] ?? []);
                @endphp

                <ul class="space-y-4">
                    <li class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600 font-medium font-bn">Total Students</span>
                        <span class="font-bold text-lg text-indigo-600 counter-up" data-count="{{ $totalStudents }}">0</span>
                    </li>
                    <li class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600 font-medium font-bn">Boys</span>
                        <span class="font-bold text-gray-800 counter-up" data-count="{{ $genderData['Male'] ?? 0 }}">0</span>
                    </li>
                    <li class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600 font-medium font-bn">Girls</span>
                        <span class="font-bold text-gray-800 counter-up" data-count="{{ $genderData['Female'] ?? 0 }}">0</span>
                    </li>
                    <li class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600 font-medium font-bn">Muslim</span>
                        <span class="font-bold text-gray-800 counter-up" data-count="{{ $religionData['Islam'] ?? 0 }}">0</span>
                    </li>
                    <li class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600 font-medium font-bn">Hindu</span>
                        <span class="font-bold text-gray-800 counter-up" data-count="{{ $religionData['Hindu'] ?? 0 }}">0</span>
                    </li>
                    <li class="flex justify-between items-center py-2">
                        <span class="text-gray-600 font-medium font-bn">Other</span>
                        <span class="font-bold text-gray-800 counter-up" 
                              data-count="{{ ($religionData['Christian'] ?? 0) + ($religionData['Buddhism'] ?? 0) }}">0</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>