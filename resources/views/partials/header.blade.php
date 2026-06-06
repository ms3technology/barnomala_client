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
    $instituteCode = $options['institute.identity.code'] ?? null;
    $centerCode = $options['institute.identity.center_code'] ?? null;
    $logoUrl = $options['institute.branding.logo_json']['url'] ?? ($options['logo_url'] ?? asset('images/school-logo.png'));
    $headerBg = $options['institute.branding.header_bg'] ?? '#ffffff';
    $schoolTenantId = trim($options['institute.tenant.id'] ?? request()->getHost());
    $portalLoginUrl = 'https://cloud.barnomala.com/login?school=' . rawurlencode($schoolTenantId !== '' ? $schoolTenantId : 'demo');

    $facebook  = $options['institute.social.facebook']  ?? '';
    $youtube   = $options['institute.social.youtube']   ?? '';
    $whatsapp  = $options['institute.social.whatsapp']  ?? $phone;
    $instagram = $options['institute.social.instagram'] ?? '';
    $linkedin  = $options['institute.social.linkedin']  ?? '';
    $twitter   = $options['institute.social.twitter']   ?? '';
    if (str_starts_with($whatsapp, '01')) {
        $whatsapp = '+88' . $whatsapp;
    }
    $waDigits = preg_replace('/[^0-9]/', '', $whatsapp);
    $applyUrl = \Illuminate\Support\Facades\Route::has('apply.index') ? route('apply.index') : url('/apply');
@endphp

<header class="overflow-visible relative z-50 transition-colors duration-300" style="background-color: {{ $headerBg }};" x-data="{ mobileMenuOpen: false }">
    <!-- Top Contact Bar -->
    <div class="bg-gray-800 text-white border-b border-white/10">
        <div class="max-w-[90%] mx-auto px-0 md:px-6 lg:px-8">
            <!-- Desktop: phone, email, address -->
            <div class="hidden md:flex items-center justify-between gap-6 h-9 text-xs">
                <div class="flex items-center gap-6 min-w-0">
                    <a href="tel:{{ $phone }}" class="flex items-center gap-2 hover:text-yellow-400 transition-colors shrink-0">
                        <svg class="w-4 h-4 text-yellow-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        <span class="font-semibold tracking-wide">{{ $phone }}</span>
                    </a>
                    <a href="mailto:{{ $email }}" class="flex items-center gap-2 hover:text-yellow-400 transition-colors min-w-0">
                        <svg class="w-4 h-4 text-yellow-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span class="font-semibold tracking-wide truncate">{{ $email }}</span>
                    </a>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <div class="flex items-center gap-4">
                        @if($facebook)
                            <a href="{{ $facebook }}" target="_blank" rel="noopener" title="Facebook" class="text-white/80 hover:text-yellow-400 transition-colors">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22 12.07C22 6.51 17.52 2 12 2S2 6.51 2 12.07c0 5.02 3.66 9.18 8.44 9.93v-7.02H7.9v-2.91h2.54V9.84c0-2.52 1.49-3.91 3.78-3.91 1.1 0 2.24.2 2.24.2v2.47h-1.26c-1.24 0-1.63.77-1.63 1.56v1.88h2.77l-.44 2.91h-2.33V22c4.78-.75 8.43-4.91 8.43-9.93z"/></svg>
                            </a>
                        @endif
                        @if($youtube)
                            <a href="{{ $youtube }}" target="_blank" rel="noopener" title="YouTube" class="text-white/80 hover:text-yellow-400 transition-colors">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23.5 6.2a3 3 0 0 0-2.1-2.12C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.58A3 3 0 0 0 .5 6.2 31.6 31.6 0 0 0 0 12a31.6 31.6 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.12C4.5 20.5 12 20.5 12 20.5s7.5 0 9.4-.58a3 3 0 0 0 2.1-2.12c.33-1.9.5-3.83.5-5.8 0-1.97-.17-3.9-.5-5.8zM9.6 15.6V8.4l6.3 3.6-6.3 3.6z"/></svg>
                            </a>
                        @endif
                        @if($whatsapp)
                            <a href="https://wa.me/{{ $waDigits }}" target="_blank" rel="noopener" title="WhatsApp" class="text-white/80 hover:text-yellow-400 transition-colors">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M.05 24l1.69-6.16A11.87 11.87 0 0 1 .06 11.1C.06 4.98 4.98.06 11.1.06c2.95 0 5.72 1.15 7.8 3.23a11 11 0 0 1 3.23 7.81c-.01 6.12-4.94 11.04-11.06 11.04h-.01a11 11 0 0 1-5.27-1.35L.05 24zM6.6 20.13l.34.2a9.04 9.04 0 0 0 4.55 1.22h.01c5.02 0 9.1-4.08 9.11-9.1a9.05 9.05 0 0 0-2.66-6.42 9.05 9.05 0 0 0-6.42-2.66c-5.02 0-9.1 4.08-9.1 9.1 0 1.6.42 3.16 1.22 4.54l.22.36-1.18 4.3 4.4-1.16zM17.2 14.4c-.27-.14-1.62-.8-1.87-.89-.25-.09-.43-.14-.62.14-.18.27-.71.89-.87 1.07-.16.18-.32.2-.59.07-.27-.14-1.15-.42-2.19-1.35-.81-.72-1.36-1.61-1.52-1.88-.16-.27-.02-.42.12-.55.12-.12.27-.32.41-.48.14-.16.18-.27.27-.45.09-.18.05-.34-.02-.48-.07-.14-.62-1.5-.85-2.05-.22-.54-.45-.46-.62-.47l-.53-.01a1.02 1.02 0 0 0-.74.34c-.25.27-.97.95-.97 2.31 0 1.36 1 2.68 1.14 2.87.14.18 1.97 3 4.77 4.21.67.29 1.19.46 1.59.59.67.21 1.27.18 1.75.11.53-.08 1.62-.66 1.85-1.3.23-.64.23-1.19.16-1.3-.07-.11-.25-.18-.52-.32z"/></svg>
                            </a>
                        @endif
                        @if($instagram)
                            <a href="{{ $instagram }}" target="_blank" rel="noopener" title="Instagram" class="text-white/80 hover:text-yellow-400 transition-colors">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.16c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23a3.7 3.7 0 0 1-.9 1.38c-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41a3.7 3.7 0 0 1-1.38-.9 3.7 3.7 0 0 1-.9-1.38c-.16-.42-.36-1.06-.41-2.23C2.17 15.58 2.16 15.2 2.16 12s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41C8.42 2.17 8.8 2.16 12 2.16M12 0C8.74 0 8.33.01 7.05.07 5.78.13 4.9.33 4.14.63a5.85 5.85 0 0 0-2.12 1.38A5.85 5.85 0 0 0 .63 4.14C.33 4.9.13 5.78.07 7.05.01 8.33 0 8.74 0 12s.01 3.67.07 4.95c.06 1.27.26 2.15.56 2.91.31.79.73 1.46 1.38 2.12a5.85 5.85 0 0 0 2.12 1.38c.76.3 1.64.5 2.91.56C8.33 23.99 8.74 24 12 24s3.67-.01 4.95-.07c1.27-.06 2.15-.26 2.91-.56a5.85 5.85 0 0 0 2.12-1.38 5.85 5.85 0 0 0 1.38-2.12c.3-.76.5-1.64.56-2.91.06-1.28.07-1.69.07-4.95s-.01-3.67-.07-4.95c-.06-1.27-.26-2.15-.56-2.91A5.85 5.85 0 0 0 21.98 2.01 5.85 5.85 0 0 0 19.86.63c-.76-.3-1.64-.5-2.91-.56C15.67.01 15.26 0 12 0zm0 5.84a6.16 6.16 0 1 0 0 12.32 6.16 6.16 0 0 0 0-12.32zm0 10.16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.4-11.85a1.44 1.44 0 1 0 0 2.88 1.44 1.44 0 0 0 0-2.88z"/></svg>
                            </a>
                        @endif
                        @if($linkedin)
                            <a href="{{ $linkedin }}" target="_blank" rel="noopener" title="LinkedIn" class="text-white/80 hover:text-yellow-400 transition-colors">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.45 20.45h-3.55v-5.57c0-1.33-.03-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.36V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.06 2.06 0 1 1 0-4.12 2.06 2.06 0 0 1 0 4.12zM7.12 20.45H3.56V9h3.56v11.45zM22.22 0H1.77C.79 0 0 .77 0 1.72v20.56C0 23.23.79 24 1.77 24h20.45c.98 0 1.78-.77 1.78-1.72V1.72C24 .77 23.2 0 22.22 0z"/></svg>
                            </a>
                        @endif
                        @if($twitter)
                            <a href="{{ $twitter }}" target="_blank" rel="noopener" title="Twitter" class="text-white/80 hover:text-yellow-400 transition-colors">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231 5.45-6.231zm-1.161 17.52h1.833L7.084 4.126H5.117l11.966 15.644z"/></svg>
                            </a>
                        @endif
                    </div>
                    <span class="w-px h-5 bg-white/15"></span>
                    <a href="{{ $applyUrl }}" class="animate-pulse hover:animate-none inline-flex items-center gap-1.5 bg-indigo-400 hover:bg-indigo-300 text-indigo-950 font-extrabold uppercase tracking-wider text-[10px] xl:text-[11px] px-3 py-1 rounded-md shadow-sm transition-colors">
                        Online Apply
                    </a>
                </div>
            </div>
            <!-- Mobile: phone & email & apply -->
            <div class="flex md:hidden items-center justify-between gap-3 h-9 text-[11px]">
                <a href="tel:{{ $phone }}" class="flex items-center gap-1.5 hover:text-yellow-400 transition-colors shrink-0">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    <span class="font-semibold truncate">{{ $phone }}</span>
                </a>
                <a href="mailto:{{ $email }}" class="flex items-center gap-1.5 hover:text-yellow-400 transition-colors min-w-0">
                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span class="font-semibold truncate">{{ $email }}</span>
                </a>
                <a href="{{ $applyUrl }}" class="shrink-0 inline-flex items-center bg-yellow-400 hover:bg-yellow-300 text-indigo-950 font-extrabold uppercase tracking-wider text-[10px] px-2.5 py-1 rounded-md transition-colors">
                    Apply
                </a>
            </div>
        </div>
    </div>

    <!-- Banner and Overlay Information -->
    <div class="max-w-[90%] mx-auto px-0 md:px-4 lg:px-8">
        <!-- 1. Only Banner Image -->
        @if ($bannerType === 'banner_only')
            <div class="relative group overflow-hidden shadow-2xl transition-all duration-500">
                <a href="{{ route('home') }}" class="block">
                    <img src="{{ $bannerUrl }}" 
                         alt="Institute Banner" 
                         class="w-full h-auto object-contain transform transition-transform duration-700 group-hover:scale-105">
                </a>
            </div>

        <!-- 2. Banner Left and Info Right -->
        @elseif ($bannerType === 'banner_split')
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 bg-white">
                <div class="overflow-hidden shadow-lg group w-full md:w-auto">
                    <a href="{{ route('home') }}">
                        <img src="{{ $bannerUrl }}" 
                             alt="Institute Banner" 
                             class="w-full h-auto object-contain transform transition-transform duration-700 group-hover:scale-105">
                    </a>
                </div>
                <div class="flex md:flex-col gap-4 pb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 md:w-12 md:h-12 rounded-full bg-indigo-600 flex items-center justify-center text-white shadow-md">
                            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-[8px] md:text-xs font-bold text-indigo-900/50 uppercase tracking-widest">Call Us</h4>
                            <p class="text-[8px] md:text-lg font-black text-indigo-900">{{ $phone }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-7 h-7 md:w-12 md:h-12 rounded-full bg-yellow-500 flex items-center justify-center text-indigo-950 shadow-md">
                            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-[8px] md:text-xs font-bold text-yellow-700/50 uppercase tracking-widest">Email Us</h4>
                            <p class="text-[8px] md:text-lg font-black text-indigo-900">{{ $email }}</p>
                        </div>
                    </div>
                </div>
            </div>

        <!-- 3. Only Informations -->
        @elseif ($bannerType === 'info_only' || $bannerType === 'banner_with_overlay')
            <div class="relative {{ $bannerType === 'banner_with_overlay' ? 'overflow-hidden shadow-2xl' : 'bg-white' }} py-4">

                @if ($bannerType === 'banner_with_overlay')
                    <a href="{{ route('home') }}" class="absolute inset-0 block" aria-label="Institute Banner">
                        <img src="{{ $bannerUrl }}"
                             alt="Institute Banner"
                             class="w-full h-full object-cover transform transition-transform duration-700 hover:scale-105">
                    </a>
                    <div class="absolute inset-0 bg-white/80"></div>
                @endif

                <div class="relative flex flex-row items-center justify-between gap-4 md:gap-10">
                    <!-- Left: Logo and Basic Identity -->
                    <div class="flex items-center gap-3 md:gap-6 flex-1">
                        <div class="relative group shrink-0 ml-2 md:ml-0">
                            <div class="absolute -inset-2 bg-indigo-600/10 rounded-full blur-xl group-hover:bg-indigo-600/20 transition-all duration-500"></div>
                            <img src="{{ $logoUrl }}" alt="Logo" class="relative w-12 h-12 md:w-24 md:h-24 lg:w-28 lg:h-28 object-contain drop-shadow-2xl transform transition-transform duration-500 group-hover:scale-110">
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-lg md:text-3xl lg:text-4xl font-black text-slate-700 uppercase drop-shadow-sm">{{ $instituteName }}</h1>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                                <p class="hidden sm:flex text-xs md:text-sm font-semibold text-gray-800 items-center gap-1 font-sans">
                                    <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ $address }}
                                </p>
                            </div>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 md:mt-2">
                                <span class="bg-indigo-900 text-white text-[8px] md:text-xs font-bold px-2 md:px-3 py-0.5 md:py-1 rounded-full shadow-sm">EIIN: {{ $eiin }}</span>
                                <span class="bg-yellow-500 text-indigo-950 text-[8px] md:text-xs font-bold px-2 md:px-3 py-0.5 md:py-1 rounded-full shadow-sm">ESTD: {{ $estd }}</span>
                                @if($instituteCode)
                                    <span class="bg-emerald-600 text-white text-[8px] md:text-xs font-bold px-2 md:px-3 py-0.5 md:py-1 rounded-full shadow-sm">CODE: {{ $instituteCode }}</span>
                                @endif
                                @if($centerCode)
                                    <span class="bg-rose-600 text-white text-[8px] md:text-xs font-bold px-2 md:px-3 py-0.5 md:py-1 rounded-full shadow-sm">CENTER: {{ $centerCode }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Navigation Bar -->
    <div class="relative z-60 bg-accent border-b border-white/10 shadow-lg">
        <nav class="max-w-[90%] mx-auto px-0 md:px-6 lg:px-8">
            <div class="relative transition-all duration-300">
                <div class="flex items-center justify-between h-10 px-0 md:px-4">
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
                                      class="px-3 py-1 uppercase tracking-widest text-white font-semibold transition-all rounded-md hover:bg-white/10 text-xs xl:text-sm">
                                    {{ $item['label'] }}
                                </a>

                            <!-- Dropdown -->
                            @else
                                <div class="relative group px-3 py-1 cursor-pointer font-bold text-white transition-all rounded-md hover:bg-white/10 group text-xs xl:text-sm">
                                    <div class="flex items-center gap-1 uppercase tracking-widest font-semibold">
                                        <span>{{ $item['label'] }}</span>
                                        <svg class="w-3 h-3 transform group-hover:rotate-180 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                    <div class="absolute hidden group-hover:block top-full left-0 w-48 pt-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <div class="bg-white text-gray-800 shadow-2xl rounded-lg overflow-hidden border border-gray-100 transform origin-top scale-95 group-hover:scale-100 transition-all duration-300 border-t-2 border-t-yellow-500">
                                            @foreach ($item['children'] as $child)
                                                <a href="{{ $child['url'] }}" 
                                                      class="block px-4 py-2 hover:bg-indigo-50 hover:text-indigo-600 transition-colors border-b border-gray-50 last:border-0 text-sm">
                                                    {{ $child['label'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    
                    {{-- PORTAL LOGIN with Admin Panel reveal --}}
                    <div class="relative ml-4 flex group/login">                        
                        <a href="{{ route('admin.dashboard') }}"
                           class="opacity-0 group-hover/login:opacity-100 px-3 py-1.5 text-white font-bold transition-all rounded-md hover:bg-white/5 text-sm hover:bg-indigo-800">
                            Web Panel
                        </a>
                        <a href="{{ $portalLoginUrl }}" target="_blank" class="block px-3 py-1.5 text-white font-bold transition-all rounded-md hover:bg-white/5 text-sm">
                            PORTAL LOGIN
                        </a>
                    </div>
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
                    <a href="{{ $portalLoginUrl }}" target="_blank" class="block px-4 py-2 text-sm font-bold hover:bg-white/10 rounded-md transition-all">
                        Portal Login
                    </a>
                </div>
            </div>
        </nav>
    </div>
</header>
