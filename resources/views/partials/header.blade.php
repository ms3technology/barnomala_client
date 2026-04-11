@php
    $bannerType = $options['institute.branding.banner_type'] ?? 'banner_with_overlay';
    $bannerUrl = $options['institute.branding.banner_json']['url'] ?? ($options['logo_url'] ?? asset('images/banner.png'));
    $phone = $options['institute.contact.phone'] ?? ($options['phone'] ?? '01234-567890');
    $phone2 = $options['institute.contact.phone_extra'] ?? '01700-000000';
    $email = $options['institute.contact.email'] ?? ($options['email'] ?? 'info@school.edu.bd');
    $address = $options['institute.contact.address'] ?? 'আপনার প্রতিষ্ঠানের ঠিকানা এখানে লিখুন';
    $instituteName = $options['institute.branding.name'] ?? '????? ???????????? ???';
    $eiin = $options['institute.identity.eiin'] ?? ($options['institute.eiin'] ?? '123456');
    $estd = $options['institute.identity.established_year'] ?? ($options['institute.estd_year'] ?? '1995');
    $logoUrl = $options['institute.branding.logo_json']['url'] ?? ($options['logo_url'] ?? asset('images/school-logo.png'));
    $headerBg = $options['institute.branding.header_bg'] ?? '#ffffff';
    $schoolTenantId = trim($options['institute.tenant.id'] ?? request()->getHost());
    $portalLoginUrl = 'https://cloud.barnomala.com/login?school=' . rawurlencode($schoolTenantId !== '' ? $schoolTenantId : 'demo');
@endphp

<header class="overflow-visible relative z-50 transition-colors duration-300" style="background-color: {{ $headerBg }};" x-data="{ mobileMenuOpen: false }">
    <!-- Banner and Overlay Information -->
    <div class="max-w-7xl mx-auto md:px-4 sm:px-6 lg:px-8">
        <!-- 1. Only Banner Image -->
        @if ($bannerType === 'banner_only')
            <div class="relative group overflow-hidden shadow-2xl transition-all duration-500">
                <a href="{{ route('home') }}" class="block">
                    <img src="{{ $bannerUrl }}" 
                         alt="Institute Banner" 
                         class="w-full h-auto object-contain transform transition-transform duration-700 group-hover:scale-105">
                </a>
            </div>

        <!-- 2. Banner with Info Overlay (Original Design) -->
        @elseif ($bannerType === 'banner_with_overlay')
            <div class="relative group overflow-hidden shadow-2xl transition-all duration-500">
                <!-- Banner Image -->
                <a href="{{ route('home') }}" class="block">
                    <img src="{{ $bannerUrl }}" 
                         alt="Institute Banner" 
                         class="w-full h-auto object-contain transform transition-transform duration-700 group-hover:scale-105">
                </a>

                <!-- Information Overlay -->
                <div class="absolute inset-0 bg-linear-to-r from-indigo-900/90 via-indigo-900/40 to-transparent flex items-center">
                    <div class="pl-6 md:pl-12 py-4">
                        <div class="flex flex-col gap-4 md:gap-6">
                            <!-- Call Info -->
                            <div class="flex items-center gap-4 group/item">
                                <div class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-white/10 backdrop-blur-md flex items-center justify-center text-yellow-400 shadow-lg border border-white/20 transition-all duration-300 group-hover/item:bg-yellow-400 group-hover/item:text-indigo-900 group-hover/item:scale-110">
                                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </div>
                                <div class="drop-shadow-md">
                                    <p class="text-[10px] md:text-xs font-bold text-indigo-200 uppercase tracking-[0.2em]">Contact Support</p>
                                    <p class="text-sm md:text-xl font-black text-white tracking-tight">{{ $phone }}</p>
                                </div>
                            </div>

                            <!-- Email Info -->
                            <div class="flex items-center gap-4 group/item">
                                <div class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-white/10 backdrop-blur-md flex items-center justify-center text-yellow-400 shadow-lg border border-white/20 transition-all duration-300 group-hover/item:bg-yellow-400 group-hover/item:text-indigo-900 group-hover/item:scale-110">
                                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <div class="drop-shadow-md">
                                    <p class="text-[10px] md:text-xs font-bold text-indigo-200 uppercase tracking-[0.2em]">Official Email</p>
                                    <p class="text-sm md:text-xl font-black text-white tracking-tight">{{ $email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- 3. Banner Left and Info Right -->
        @elseif ($bannerType === 'banner_split')
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 bg-white">
                <div class="overflow-hidden shadow-lg group w-full md:w-auto">
                    <a href="{{ route('home') }}">
                        <img src="{{ $bannerUrl }}" 
                             alt="Institute Banner" 
                             class="w-full h-auto object-contain transform transition-transform duration-700 group-hover:scale-105">
                    </a>
                </div>
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-indigo-600 flex items-center justify-center text-white shadow-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-indigo-900/50 uppercase tracking-widest">Call Us</h4>
                            <p class="text-lg font-black text-indigo-900">{{ $phone }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-yellow-500 flex items-center justify-center text-indigo-950 shadow-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-yellow-700/50 uppercase tracking-widest">Email Us</h4>
                            <p class="text-lg font-black text-indigo-900">{{ $email }}</p>
                        </div>
                    </div>
                </div>
            </div>

        <!-- 4. Only Informations -->
        @elseif ($bannerType === 'info_only')
            <div class="bg-white py-4 md:py-6 lg:py-8">
                <div class="flex flex-row items-center justify-between gap-4 md:gap-10 px-4 md:px-0">
                    <!-- Left: Logo and Basic Identity -->
                    <div class="flex items-center gap-3 md:gap-6 flex-1">
                        <div class="relative group shrink-0 ml-2 md:ml-0">
                            <div class="absolute -inset-2 bg-indigo-600/10 rounded-full blur-xl group-hover:bg-indigo-600/20 transition-all duration-500"></div>
                            <img src="{{ $logoUrl }}" alt="{{ $instituteName }}" class="relative w-12 h-12 md:w-24 md:h-24 lg:w-28 lg:h-28 object-contain drop-shadow-2xl transform transition-transform duration-500 group-hover:scale-110">
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-lg md:text-3xl lg:text-4xl font-black text-indigo-950 tracking-tight leading-tight uppercase font-sans drop-shadow-sm">{{ $instituteName }}</h1>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 md:mt-2">
                                <span class="bg-indigo-900 text-white text-[8px] md:text-xs font-bold px-2 md:px-3 py-0.5 md:py-1 rounded-full shadow-sm">EIIN: {{ $eiin }}</span>
                                <span class="bg-yellow-500 text-indigo-950 text-[8px] md:text-xs font-bold px-2 md:px-3 py-0.5 md:py-1 rounded-full shadow-sm">ESTD: {{ $estd }}</span>
                                <p class="hidden sm:flex text-xs md:text-sm font-semibold text-indigo-900/60 items-center gap-1 font-sans">
                                    <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ $address }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Quick Contact -->
                    <div class="hidden lg:flex flex-col gap-2 shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm border border-indigo-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[9px] font-bold text-indigo-900/40 uppercase tracking-widest leading-none mb-0.5">Contact</span>
                                <p class="text-sm font-black text-indigo-950 leading-none">{{ $phone }}, {{ $phone2 }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-yellow-50 flex items-center justify-center text-yellow-600 shadow-sm border border-yellow-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[9px] font-bold text-indigo-900/40 uppercase tracking-widest leading-none mb-0.5">Email Us</span>
                                <p class="text-sm font-black text-indigo-950 leading-none">{{ $email }}</p>
                            </div>
                        </div>
                        @if($facebookUrl = $options['social.facebook'] ?? null)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-yellow-50 flex items-center justify-center text-yellow-600 shadow-sm border border-yellow-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[9px] font-bold text-indigo-900/40 uppercase tracking-widest leading-none mb-0.5">Facebook</span>
                                <p class="text-sm font-black text-indigo-950 leading-none">{{ $facebookUrl }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Navigation Bar -->
    <div class="relative z-50 bg-accent border-b border-white/10 shadow-lg">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative transition-all duration-300">
                <div class="flex items-center justify-between h-10 md:h-12 px-2 md:px-4">
                    <!-- Mobile Menu Button -->
                    <div class="flex md:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" 
                                class="text-white hover:text-yellow-400 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white p-1 rounded-md transition-colors"
                                aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg x-show="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                            </svg>
                            <svg x-show="mobileMenuOpen" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Desktop Menu -->
                    <div class="hidden md:flex items-center gap-0.5">
                        @foreach ($navigationItems ?? [] as $item)
                            <!-- Single Link -->
                            @if (empty($item['children']))
                                <a href="{{ $item['url'] }}" 
                                      class="px-3 py-1.5 text-white hover:text-yellow-400 font-bold transition-all rounded-md hover:bg-white/5 text-xs xl:text-sm">
                                    {{ $item['label'] }}
                                </a>

                            <!-- Dropdown -->
                            @else
                                <div class="relative group px-3 py-1.5 cursor-pointer font-bold text-white transition-all rounded-md hover:bg-white/5 group text-xs xl:text-sm">
                                    <div class="flex items-center gap-1">
                                        <span>{{ $item['label'] }}</span>
                                        <svg class="w-3 h-3 transform group-hover:rotate-180 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                    <div class="absolute hidden group-hover:block top-full left-0 w-48 pt-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <div class="bg-white text-gray-800 shadow-2xl rounded-lg overflow-hidden border border-gray-100 transform origin-top scale-95 group-hover:scale-100 transition-all duration-300 border-t-2 border-t-yellow-500">
                                            @foreach ($item['children'] as $child)
                                                <a href="{{ $child['url'] }}" 
                                                      class="block px-4 py-2 hover:bg-indigo-50 hover:text-indigo-600 transition-colors border-b border-gray-50 last:border-0 text-xs">
                                                    {{ $child['label'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    
                    {{-- PORTAL LOGIN --}}
                    <a href="{{ $portalLoginUrl }}" target="_blank" class="ml-4 px-3 py-1.5 text-white hover:text-yellow-400 font-bold transition-all rounded-md hover:bg-white/5 text-xs xl:text-sm">
                        Portal Login
                    </a>
                </div>
            </div>

            <div x-show="mobileMenuOpen" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-4"
                 class="md:hidden pb-4 space-y-1">
                @foreach ($navigationItems ?? [] as $item)
                    @if (empty($item['children']))
                        <a href="{{ $item['url'] }}" 
                           class="block px-4 py-2 text-sm font-bold text-white hover:bg-white/10 rounded-md transition-all">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <div x-data="{ open: false }" class="space-y-1">
                            <button @click="open = !open" 
                                    class="w-full flex items-center justify-between px-4 py-2 text-sm font-bold text-white hover:bg-white/10 rounded-md transition-all">
                                <span>{{ $item['label'] }}</span>
                                <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-cloak class="pl-4 space-y-1">
                                @foreach ($item['children'] as $child)
                                    <a href="{{ $child['url'] }}" 
                                       class="block px-4 py-2 text-xs font-medium text-indigo-100 hover:bg-white/10 rounded-md transition-all">
                                        {{ $child['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
                <div class="pt-4 mt-4 border-t border-white/10">
                    <a href="{{ $portalLoginUrl }}" target="_blank" class="block px-4 py-2 text-sm font-bold text-yellow-400 hover:bg-white/10 rounded-md transition-all">
                        Portal Login
                    </a>
                </div>
            </div>
        </nav>
    </div>
</header>
