@php
    $schoolName = $options['institute.branding.name'] ?? ($options['institute.name'] ?? config('app.name', 'Barnomala'));
    $shortName = $options['institute.short_name'] ?? 'Barnomala';
    $footerText = $options['institute.footer.text'] ?? ($options['institute.about.text'] ?? ($options['aboutUsText'] ?? '??????? ?????? ?????? ???????????? ????? ?????? ???? ?????? ?????? ?????????? ????? ?????? ???? ??? ? ???? ??????? ????????'));
    $phone = $options['institute.contact.phone'] ?? ($options['phone'] ?? '01234-567890');
    $email = $options['institute.contact.email'] ?? ($options['email'] ?? 'info@school.edu.bd');
    $eiin = $options['institute.eiin'] ?? ($options['institute.identity.eiin'] ?? 'N/A');
    $code = $options['institute.code'] ?? 'N/A';
    $logoUrl = $options['institute.logo_json']['url'] ?? ($options['logo_url'] ?? asset('images/school-logo.png'));
    $visitorCount = \App\Models\Option::get('site.visitor_count', 0);
@endphp

<footer class="bg-[#002147] border-t-4 border-yellow-500 text-white font-sans tracking-wide relative overflow-hidden mt-20">
    <!-- Background decoration -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-[30%] -left-[10%] w-[50%] h-[50%] bg-blue-900/40 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[10%] -right-[10%] w-[60%] h-[60%] bg-yellow-500/5 rounded-full blur-[150px]"></div>
    </div>

    <!-- Main Footer -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-12 gap-y-16 relative z-10">
        <!-- Institutional Info -->
        <div class="space-y-8">
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-4">
                    <div class="p-2.5 bg-white/5 rounded-2xl backdrop-blur-md border border-white/10 shadow-xl">
                        <img src="{{ $logoUrl }}" alt="Logo" class="w-14 h-14 object-contain filter brightness-0 invert opacity-90">
                    </div>
                    <h3 class="text-2xl font-black uppercase tracking-[0.15em] text-white">{{ $shortName }}</h3>
                </div>
                <p class="text-blue-100/80 text-sm leading-relaxed max-w-sm font-medium font-bn">
                    {{ $footerText }}
                </p>
            </div>
            <div class="space-y-4 pt-2">
                <div class="flex items-center gap-4 group">
                    <span class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center group-hover:bg-indigo-500/30 transition-all duration-300 border border-white/5 group-hover:border-indigo-400/50 shadow-lg">
                        <svg class="w-4 h-4 text-indigo-300 group-hover:text-yellow-400 group-hover:scale-110 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    </span>
                    <span class="text-sm font-semibold text-indigo-100 group-hover:text-white transition-colors tracking-wide">{{ $phone }}</span>
                </div>
                <div class="flex items-center gap-4 group">
                    <span class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center group-hover:bg-indigo-500/30 transition-all duration-300 border border-white/5 group-hover:border-indigo-400/50 shadow-lg">
                        <svg class="w-4 h-4 text-indigo-300 group-hover:text-yellow-400 group-hover:scale-110 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </span>
                    <span class="text-sm font-semibold text-indigo-100 group-hover:text-white transition-colors tracking-wide">{{ $email }}</span>
                </div>
            </div>
        </div>

        <!-- Useful Links -->
        <div class="space-y-6">
            <div class="inline-block relative">
                <h4 class="text-lg font-bold uppercase tracking-wider text-white">Navigation</h4>
                <div class="absolute -bottom-2 left-0 w-1/2 h-1 bg-linear-to-r from-yellow-400 to-transparent rounded-full"></div>
            </div>
            <ul class="space-y-4 mt-8">
                @foreach (['Notice Board', 'Admission', 'Academic Calendar', 'Curriculum', 'Gallery'] as $item)
                    <li>
                        <a href="#" class="text-sm font-medium text-indigo-200/80 hover:text-white transition-all flex items-center gap-3 group transform hover:translate-x-2">
                            <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full group-hover:bg-yellow-400 group-hover:scale-150 transition-all duration-300 shadow-[0_0_8px_rgba(250,204,21,0.5)]"></span>
                            {{ $item }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Important Boards -->
        <div class="space-y-6">
            <div class="inline-block relative">
                <h4 class="text-lg font-bold uppercase tracking-wider text-white">Board Links</h4>
                <div class="absolute -bottom-2 left-0 w-1/2 h-1 bg-linear-to-r from-yellow-400 to-transparent rounded-full"></div>
            </div>
            <div class="space-y-4 mt-8">
                @foreach (array_slice($importantLinks ?? [], 0, 5) as $link)
                    <a href="{{ $link['url'] }}" target="_blank" rel="noreferrer"
                       class="text-sm font-medium text-indigo-200/80 hover:text-white transition-all flex items-center gap-3 group transform hover:translate-x-2">
                        <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full group-hover:bg-yellow-400 group-hover:scale-150 transition-all duration-300 shadow-[0_0_8px_rgba(250,204,21,0.5)]"></span>
                        {{ $link['title'] }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- External Portals -->
        <div class="space-y-6">
            <div class="inline-block relative">
                <h4 class="text-lg font-bold uppercase tracking-wider text-white">Public Portals</h4>
                <div class="absolute -bottom-2 left-0 w-1/2 h-1 bg-linear-to-r from-yellow-400 to-transparent rounded-full"></div>
            </div>
            <div class="grid grid-cols-2 gap-3 mt-8">
                @foreach (['DSHE', 'BANBEIS', 'MOEDU', 'A2I'] as $link)
                    <a href="#" target="_blank"
                       class="px-4 py-3 bg-white/5 hover:bg-indigo-600 text-xs font-bold text-center rounded-xl transition-all duration-300 border border-white/5 hover:border-indigo-400 hover:shadow-lg hover:shadow-indigo-500/30 backdrop-blur-sm group">
                        <span class="text-indigo-100 group-hover:text-white">{{ $link }}</span>
                    </a>
                @endforeach
            </div>
            <div class="pt-8 mt-8 border-t border-white/10 space-y-5">
                <p class="text-[10px] text-indigo-300 font-bold uppercase tracking-[0.25em]">Institution IDs</p>
                <div class="flex items-center gap-3">
                    <div class="bg-black/20 p-3.5 rounded-xl border border-white/5 flex-1 relative overflow-hidden group hover:border-white/20 transition-colors">
                        <span class="block text-[9px] text-indigo-400 uppercase font-bold tracking-widest mb-1">EIIN</span>
                        <span class="block text-xl font-black tracking-widest text-white leading-none drop-shadow-md group-hover:text-yellow-400 transition-colors">{{ $eiin }}</span>
                    </div>
                    <div class="bg-black/20 p-3.5 rounded-xl border border-white/5 flex-1 relative overflow-hidden group hover:border-white/20 transition-colors">
                        <span class="block text-[9px] text-indigo-400 uppercase font-bold tracking-widest mb-1">CODE</span>
                        <span class="block text-xl font-black tracking-widest text-white leading-none drop-shadow-md group-hover:text-yellow-400 transition-colors">{{ $code }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sub Footer -->
    <div class="border-t border-white/5 bg-black/40 text-indigo-300/80 text-sm py-8 backdrop-blur-md relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="order-last md:order-first text-center md:text-left font-medium">
                &copy; {{ now()->year }} <span class="font-bold text-white tracking-wide">{{ $schoolName }}</span>. All rights reserved.<br>
                <span class="text-xs mt-1.5 block">Website designed & developed by <a href="https://ms3technology.com.bd" target="_blank" class="text-yellow-400 hover:text-yellow-300 font-bold transition-colors">MS3 Technology BD</a>.</span>
            </div>
            <!-- Social Links -->
            <div class="flex flex-col md:flex-row items-center gap-6">
                <!-- Visitor Count -->
                <div class="flex items-center gap-3 bg-white/5 px-4 py-2 rounded-2xl border border-white/10 hover:border-yellow-400 group transition-all duration-300">
                    <div class="w-8 h-8 rounded-full bg-yellow-500/20 flex items-center justify-center text-yellow-400 group-hover:bg-yellow-500 group-hover:text-indigo-950 transition-all duration-300">
                        <i class="fa fa-users text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold uppercase tracking-widest text-indigo-300 leading-none mb-1">Visitors</p>
                        <p class="text-lg font-black text-white leading-none tracking-tight">{{ number_format($visitorCount) }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="#" class="w-10 h-10 rounded-full border border-white/10 hover:bg-white/10 hover:border-yellow-400/50 flex items-center justify-center transition-all duration-300 bg-white/5 hover:-translate-y-1 shadow-lg group">
                        <i class="fa fa-facebook text-lg text-white group-hover:text-yellow-400 transition-colors"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full border border-white/10 hover:bg-white/10 hover:border-yellow-400/50 flex items-center justify-center transition-all duration-300 bg-white/5 hover:-translate-y-1 shadow-lg group">
                        <i class="fa fa-twitter text-lg text-white group-hover:text-yellow-400 transition-colors"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full border border-white/10 hover:bg-white/10 hover:border-yellow-400/50 flex items-center justify-center transition-all duration-300 bg-white/5 hover:-translate-y-1 shadow-lg group">
                        <i class="fa fa-university text-lg text-white group-hover:text-yellow-400 transition-colors"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>
