<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | {{ config('app.name', 'Barnomala') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>

<body class="min-h-screen bg-linear-to-br from-slate-50 via-indigo-50/30 to-slate-100 text-slate-900 antialiased flex items-center justify-center p-4 sm:p-6">
    @php
        $options = \App\Models\Option::whereIn('option_key', ['institute.tenant.id', 'institute.name', 'institute.logo.image_json'])
            ->pluck('option_value', 'option_key');

        $schoolTenantId = $options['institute.tenant.id'] ?? trim(request()->getHost());
        $schoolName = $options['institute.name'] ?? config('app.name', 'Barnomala');
        $logoJson = json_decode($options['institute.logo.image_json'] ?? '', true);
        $logoUrl = isset($logoJson['url']) ? asset('storage/' . $logoJson['url']) : null;

        $portalLoginUrl = 'https://cloud.barnomala.com/sign-in-with-barnomala/?school_key=' . rawurlencode($schoolTenantId !== '' ? $schoolTenantId : 'demo');
        
        $showError = request()->has('payload') || request()->has('signature');
    @endphp

    <div class="w-full max-w-md">
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl shadow-indigo-900/5 border border-white p-8 sm:p-10 relative overflow-hidden">
            
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-48 h-1 bg-linear-to-r from-transparent via-indigo-500 to-transparent opacity-70"></div>
            
            <div class="text-center">
                <h1 class="text-xl font-bold tracking-tight text-slate-800 sm:text-2xl">
                    {{ $schoolName }}
                </h1>
                <p class="mt-2.5 text-sm text-slate-500 font-medium">
                    পাসওয়ার্ড ছাড়া এক ক্লিকে লগইন করুন
                </p>

                @if($showError)
                    <div class="mt-6 animate-fade-in">
                        <div class="flex items-center gap-3 text-rose-700 text-left bg-rose-50/70 p-3.5 rounded-xl border border-rose-100">
                            <svg class="w-5 h-5 shrink-0 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <p class="text-xs font-semibold tracking-wide">
                                {{ $message ?? 'The request has expired or authentication failed.' }}
                            </p>
                        </div>
                    </div>
                @endif

                <div class="mt-16">
                    <button type="button" onclick="signInWithBarnomala('{{ $portalLoginUrl }}')"
                        class="group relative w-full flex items-center justify-center gap-3 cursor-pointer rounded-xl bg-linear-to-r from-indigo-600 to-violet-600 px-6 py-4 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition-all duration-200 hover:from-indigo-500 hover:to-violet-500 hover:shadow-xl hover:shadow-indigo-600/30 active:scale-[0.99] focus:outline-none">
                        
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            <polyline points="10 17 15 12 10 7"></polyline>
                            <line x1="15" y1="12" x2="3" y2="12"></line>
                        </svg>
                        Sign in with Barnomala Account
                    </button>
                    
                    <p class="mt-3.5 text-xs text-slate-400">
                        One-click passwordless authentication portal
                    </p>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                 <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-[0.15em] flex items-center justify-center gap-1.5">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Powered by Barnomala Platform
                 </p>
            </div>
        </div>
    </div>

    <script>
        window.signInWithBarnomala = function(url) {
            const width = 550;
            const height = 600;
            const left = (window.innerWidth - width) / 2;
            const top = (window.innerHeight - height) / 2;

            const popup = window.open(
                url,
                'BarnomalaAuth',
                `width=${width},height=${height},top=${top},left=${left},status=no,menubar=no,toolbar=no,location=no`
            );

            if (window.focus && popup) popup.focus();

            const timer = setInterval(() => {
                if (popup && popup.closed) {
                    clearInterval(timer);
                }
            }, 1000);
        };
    </script>
</body>

</html>