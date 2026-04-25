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

    $aboutImageOption = \App\Models\Option::where('option_key', 'institute.about.image_json')->first();
    $aboutImageUrl = $aboutImageOption ? (json_decode($aboutImageOption->option_value, true)['url'] ?? asset('images/about-image.webp')) : asset('images/about-image.webp');

    $aboutSidePanelType = $options['institute.about.side_panel_type'] ?? 'image';
@endphp

<style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 font-bn reveal">
    <!-- About Us Redesign -->
    <div class="relative overflow-hidden bg-white rounded-lg shadow-[0_32px_120px_-20px_rgba(30,41,59,0.08)] mb-20 group border border-slate-100">
        <!-- Decorative Background Elements -->
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-indigo-50 rounded-full mix-blend-multiply filter blur-3xl opacity-30 group-hover:opacity-50 transition duration-1000"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-50 rounded-full mix-blend-multiply filter blur-3xl opacity-30 group-hover:opacity-50 transition duration-1000"></div>

        <div class="relative flex flex-col lg:flex-row items-stretch min-h-125">
            <!-- Content Side -->
            <div class="{{ $aboutSidePanelType === 'notice' ? 'lg:w-2/3' : 'lg:w-3/5' }} p-8 lg:p-16 flex flex-col justify-center">
                <div class="space-y-8">
                    <div>
                        <h2 class="text-3xl lg:text-4xl font-black text-slate-900 leading-[1.1] tracking-tight">
                            {{ $options['institute.about.title'] ?? 'আমাদের প্রতিষ্ঠান সম্পর্কে' }}
                        </h2>
                        <div class="w-20 h-2 bg-indigo-600 rounded-full mt-6 group-hover:w-32 transition-all duration-500"></div>
                    </div>

                    <div class="space-y-6">
                        <div class="relative group/text">
                            <div class="max-h-36 overflow-y-auto scrollbar-hide text-slate-600 text-lg leading-relaxed font-medium">
                                {{ $options['institute.about.text'] ?? 'আমাদের শিক্ষা প্রতিষ্ঠান একটি ঐতিহ্যবাহী বিদ্যাপীঠ। দীর্ঘ পথচলায় আমরা অসংখ্য মেধাবী শিক্ষার্থী উপহার দিয়েছি যারা দেশ ও দশের কল্যাণে নিয়োজিত।' }}
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 h-4 bg-linear-to-t from-white to-transparent pointer-events-none opacity-0 group-hover/text:opacity-100 transition-opacity"></div>
                        </div>
                        
                        <a href="{{ route('about') }}" 
                            class="inline-flex items-center text-xs font-black text-indigo-600 hover:text-indigo-800 transition-colors uppercase tracking-widest">
                            {{ $options['institute.about.button_text'] ?? 'Read More' }} <i class="fas fa-arrow-right ml-2 text-[10px]"></i>
                        </a>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">
                            <div class="flex items-center gap-3 text-slate-700">
                                <div class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                    <i class="fas fa-check text-[10px]"></i>
                                </div>
                                <span class="text-sm font-bold">Smart Classrooms</span>
                            </div>
                            <div class="flex items-center gap-3 text-slate-700">
                                <div class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                    <i class="fas fa-check text-[10px]"></i>
                                </div>
                                <span class="text-sm font-bold">Expert Faculty</span>
                            </div>
                            <div class="flex items-center gap-3 text-slate-700">
                                <div class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                    <i class="fas fa-check text-[10px]"></i>
                                </div>
                                <span class="text-sm font-bold">Modern Library</span>
                            </div>
                            <div class="flex items-center gap-3 text-slate-700">
                                <div class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                    <i class="fas fa-check text-[10px]"></i>
                                </div>
                                <span class="text-sm font-bold">ICT Lab</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side Panel -->
            <div class="{{ $aboutSidePanelType === 'notice' ? 'lg:w-1/3' : 'lg:w-2/5' }} relative overflow-hidden group/image">
                @if($aboutSidePanelType === 'notice')
                    <!-- Latest News Side Panel (Copied from hero-slider) -->
                    <div class="flex flex-col h-full bg-gray-50/50">
                        <div class="bg-accent text-white p-5 font-bold flex justify-between items-center shadow-lg relative overflow-hidden">
                            <div class="absolute right-0 top-0 w-32 h-32 bg-white/5 rounded-full blur-2xl -mr-10 -mt-10"></div>
                            <span class="text-xl flex items-center gap-3 relative z-10 tracking-wide">
                                <span class="w-1.5 h-6 bg-yellow-400 rounded-full inline-block shadow-[0_0_10px_rgba(250,204,21,0.5)]"></span>
                                সর্বশেষ নোটিশ
                            </span>
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <div class="p-4 space-y-3 overflow-y-auto h-full scrollbar-thin scrollbar-thumb-indigo-100 scrollbar-track-transparent">
                                @foreach($notices as $notice)
                                    <div class="group/item border border-gray-100 bg-white rounded-xl p-4 hover:bg-indigo-50/80 hover:border-indigo-100 transition-all duration-300">
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
                @else
                    <!-- Image Side -->
                    <div class="absolute inset-0 bg-indigo-600/10 z-20 group-hover/image:bg-transparent transition-colors duration-700"></div>
                    <img src="{{ $aboutImageUrl }}" alt="About Us Image" 
                         class="w-full h-full object-cover relative z-10 transition-all duration-1000 group-hover:scale-110"
                         style="clip-path: polygon(10% 0%, 100% 0%, 100% 100%, 0% 100%);">
                    
                    <!-- Floating Decorative Card -->
                    <div class="absolute bottom-10 -left-10 z-30 bg-white/90 backdrop-blur-md p-6 rounded-3xl shadow-2xl border border-white/50 transform -rotate-3 group-hover:rotate-0 transition-transform duration-500 hidden lg:block">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                                <i class="fas fa-graduation-cap text-xl"></i>
                            </div>
                            <div>
                                <div class="text-xs font-black text-indigo-600 uppercase tracking-widest leading-none">Quality</div>
                                <div class="text-lg font-black text-slate-900 mt-1">Education</div>
                            </div>
                        </div>
                    </div>
                @endif
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
                        <div class="bg-white rounded-4xl hover:shadow-[0_40px_80px_rgba(79,70,229,0.2)] transition-all duration-700 group flex flex-col border border-indigo-50/50 {{ getRowItemClass($rowConfig) }}">
                            <div class="p-10 flex-1 flex flex-col relative overflow-hidden">
                                <!-- Background Decoration -->
                                <div class="absolute -top-24 -right-24 w-48 h-48 bg-indigo-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-1000"></div>
                                
                                <div class="flex flex-col items-center mb-8 relative z-10">
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-indigo-600 rounded-full blur-lg opacity-0 group-hover:opacity-20 transition-opacity duration-500"></div>
                                        <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-2xl mb-4 relative">
                                            <img src="{{ getImageUrl($speech) }}"
                                                 class="w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-110 transition duration-700"
                                                 alt="{{ $speech['name'] ?? $speech->name }}">
                                        </div>
                                    </div>
                                    <h3 class="text-xl font-black text-indigo-950 text-center tracking-tight">{{ $speech['title'] ?? $speech->title }}</h3>
                                    <div class="w-12 h-1 bg-indigo-100 rounded-full mt-3 group-hover:w-20 group-hover:bg-indigo-600 transition-all duration-500"></div>
                                </div>

                                <div class="relative flex-1 z-10 group/speech overflow-hidden">
                                    <span class="text-7xl font-serif text-indigo-100 absolute -top-8 -left-2 select-none group-hover:text-indigo-200 transition-colors">“</span>
                                    @php
                                        $speechText = $speech['speech'] ?? $speech->speech;
                                    @endphp
                                    <div class="max-h-52 overflow-y-auto scrollbar-hide text-slate-600 italic text-center px-4 leading-relaxed bg-white">
                                        {{ $speechText }}
                                    </div>
                                    <div class="absolute bottom-0 left-0 right-0 h-8 bg-linear-to-t from-white to-transparent pointer-events-none opacity-0 group-hover/speech:opacity-100 transition-opacity"></div>
                                </div>

                                <div class="text-center mt-4">
                                    <a href="{{ route('speeches.index', ['id' => $speech['id'] ?? $speech->id]) }}" 
                                        class="inline-flex items-center text-xs font-black text-indigo-600 hover:text-indigo-800 transition-colors uppercase tracking-widest">
                                        {{ $options['institute.about.button_text'] ?? 'Read Full Message' }} <i class="fas fa-arrow-right ml-2 text-[10px]"></i>
                                    </a>
                                </div>

                                <div class="mt-8 text-right pt-6 border-t border-indigo-50 relative z-10">
                                    <div class="font-black text-indigo-950 text-lg">{{ $speech['name'] ?? $speech->name }}</div>
                                    {{-- <div class="text-[10px] text-indigo-600 font-black tracking-[0.2em] uppercase mt-1">{{ $speech['designation'] ?? $speech->designation }}</div> --}}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>
</div>
