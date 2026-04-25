@php
    $schoolName = $options['institute.branding.name'] ?? config('app.name', 'Barnomala');
    $footerText = $options['institute.footer.text'] ?? 'শোভন এবং আধুনিক ডিজাইনের সাথে আমাদের প্রতিষ্ঠান এগিয়ে যাচ্ছে আগামীর পথে।';
    $address = $options['institute.contact.address'] ?? '';
    $phone = $options['institute.contact.phone'] ?? '01234-567890';
    $email = $options['institute.contact.email'] ?? 'info@school.edu.bd';
    $eiin = $options['institute.identity.eiin'] ?? 'N/A';
    $code = $options['institute.identity.code'] ?? 'N/A';
    $logoUrl = $options['institute.branding.logo_json']['url'] ?? asset('images/school-logo.png');
    $visitorCount = \App\Models\Option::get('site.visitor_count', 0);
    $facebook = $options['institute.social.facebook'] ?? '';
    $youtube = $options['institute.social.youtube'] ?? '';
@endphp

<footer class="bg-slate-800 border-t-4 border-emerald-500 text-slate-300 font-sans tracking-wide relative overflow-hidden mt-20">
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-[30%] -left-[10%] w-[50%] h-[50%] bg-emerald-900/20 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[10%] -right-[10%] w-[60%] h-[60%] bg-cyan-900/10 rounded-full blur-[150px]"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-12 gap-y-16 relative z-10">
        <div class="space-y-8">
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-4">
                    <h3 class="text-2xl font-black uppercase tracking-[0.15em] text-white">{{ $schoolName }}</h3>
                </div>
                <p class="text-slate-400 text-sm leading-relaxed max-w-sm font-medium font-bn">
                    {{ $footerText }}
                </p>
            </div>
            <div class="flex items-center gap-4">
                <p class="text-slate-400 text-sm leading-relaxed max-w-sm font-medium font-bn">
                    {{ $address }}
                </p>
            </div>
        </div>
        
        <div class="space-y-6">
            <div class="inline-block relative">
                <h4 class="text-lg font-bold uppercase tracking-wider text-white">Contact</h4>
                <div class="absolute -bottom-2 left-0 w-1/2 h-1 bg-linear-to-r from-emerald-500 to-transparent rounded-full"></div>
            </div>
            <div class="flex items-center gap-4 group">
                <span class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center group-hover:bg-emerald-500/30 transition-all duration-300 border border-slate-700 group-hover:border-emerald-400/50 shadow-lg">
                    <svg class="w-4 h-4 text-emerald-400 group-hover:scale-110 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                </span>
                <span class="text-sm font-semibold text-slate-300 group-hover:text-emerald-400 transition-colors tracking-wide">{{ $phone }}</span>
            </div>
            <div class="flex items-center gap-4 group">
                <span class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center group-hover:bg-emerald-500/30 transition-all duration-300 border border-slate-700 group-hover:border-emerald-400/50 shadow-lg">
                    <svg class="w-4 h-4 text-emerald-400 group-hover:scale-110 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </span>
                <span class="text-sm font-semibold text-slate-300 group-hover:text-emerald-400 transition-colors tracking-wide">{{ $email }}</span>
            </div>
            <div class="{{ $facebook ? 'flex' : 'hidden' }} items-center gap-4 group">
                <span class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center group-hover:bg-emerald-500/30 transition-all duration-300 border border-slate-700 group-hover:border-emerald-400/50 shadow-lg">
                    <svg class="w-4 h-4 text-emerald-400 group-hover:scale-110 transition-all duration-300" fill="currentColor" viewBox="0 0 24 24"><path d="M22.675 0h-21.35C.597 0 0 .597 0 1.333v21.333C0 23.403.597 24 1.325 24H12.82v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.894-4.788 4.659-4.788 1.325 0 2.464.099 2.794.143v3.24l-1.918.001c-1.504 0-1.796.715-1.796 1.763v2.312h3.59l-.467 3.622h-3.123V24h6.116C23.403 24 24 23.403 24 22.667V1.333C24 .597 23.403 0 22.675 0z"/></svg>
                </span>
                <a href="{{ $facebook }}" target="_blank" class="text-sm font-semibold text-slate-300 group-hover:text-emerald-400 transition-colors tracking-wide">{{ $facebook }}</a>
            </div>
            <div class="{{ $youtube ? 'flex' : 'hidden' }} items-center gap-4 group">
                <span class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center group-hover:bg-emerald-500/30 transition-all duration-300 border border-slate-700 group-hover:border-emerald-400/50 shadow-lg">
                    <svg class="w-5 h-5 text-emerald-400 group-hover:scale-110 transition-all duration-300" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-.604-.228-1.257-.384-1.935-.432C15.412 2.64 12 2.64 12 2.64s-3.412 0-5.68.112c-.678.048-1.331.204-1.935.432C3.412 3.412 3 4.04 3 4.72v6.56c0 .68.412 1.308 1.385 1.536C5.668 12.96 9 13 9 13s3.412-.04 5.68-.112c.678-.048 1.331-.204 1.935-.432C20.588 12.608 21 11.98 21 11.3V4.72c0-.68-.412-1.308-1.385-1.536zM10 14V8l5 3-5 3z"/></svg>
                </span>
                <a href="{{ $youtube }}" target="_blank" class="text-sm font-semibold text-slate-300 group-hover:text-emerald-400 transition-colors tracking-wide">{{ $youtube }}</a>
            </div>
        </div>

        <div class="space-y-6">
            <div class="inline-block relative">
                <h4 class="text-lg font-bold uppercase tracking-wider text-white">Navigation</h4>
                <div class="absolute -bottom-2 left-0 w-1/2 h-1 bg-linear-to-r from-emerald-500 to-transparent rounded-full"></div>
            </div>
            <ul class="space-y-4 mt-8">
                @foreach (['Notice Board', 'Admission', 'Academic Calendar', 'Curriculum', 'Gallery'] as $item)
                    <li>
                        <a href="#" class="text-sm font-medium text-slate-400 hover:text-emerald-400 transition-all flex items-center gap-3 group transform hover:translate-x-2">
                            <span class="w-1.5 h-1.5 bg-slate-700 rounded-full group-hover:bg-emerald-400 group-hover:scale-150 transition-all duration-300 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                            {{ $item }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="space-y-6">
            <div class="inline-block relative">
                <h4 class="text-lg font-bold uppercase tracking-wider text-white">Public Portals</h4>
                <div class="absolute -bottom-2 left-0 w-1/2 h-1 bg-linear-to-r from-emerald-500 to-transparent rounded-full"></div>
            </div>
            <div class="mt-8 space-y-4">
                <div>Maintained By</div>
                <a href="https://barnomala.com" target="_blank" class="w-1/2 flex items-center gap-3 group p-2 rounded-lg border border-slate-800 hover:border-emerald-500 transition-all duration-300 bg-slate-900">
                    <img src="{{ asset('images/barnomala-logo.png') }}" alt="Barnomala Logo" class="object-contain filter brightness-0 invert opacity-90">
                </a>
            </div>
            <div class="pt-8 mt-8 border-t border-slate-800 space-y-5">                
                <div class="w-fit flex items-center gap-3 bg-slate-900 px-4 py-2 rounded-2xl border border-slate-800 hover:border-emerald-500 group transition-all duration-300">
                    <div class="w-8 h-8 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-400 group-hover:bg-emerald-500 group-hover:text-slate-950 transition-all duration-300">
                        <i class="fa fa-users text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold uppercase tracking-widest text-slate-500 leading-none mb-1">Visitors</p>
                        <p class="text-lg font-black text-white leading-none tracking-tight">{{ number_format($visitorCount) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="border-t border-slate-900 bg-black/40 text-slate-400 text-sm py-8 backdrop-blur-md relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-center gap-6">
            <div class="order-last md:order-first text-center font-medium">
                &copy; {{ now()->year }} <span class="font-bold text-emerald-400 tracking-wide">{{ $schoolName }}</span>. All rights reserved.<br>
                <span class="text-xs mt-1.5 block text-slate-500">Website designed & developed by <a href="https://ms3technology.com.bd" target="_blank" class="text-emerald-500 hover:text-emerald-400 font-bold transition-colors">MS3 Technology BD</a>.</span>
            </div>
        </div>
    </div>
</footer>