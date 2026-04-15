<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'SSO Error' }} | {{ config('app.name', 'Barnomala') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    @php
        $schoolTenantId = \App\Models\Option::where('option_key', 'institute.tenant.id')->value('option_value') 
            ?? trim(request()->getHost());
        $portalLoginUrl = 'https://cloud.barnomala.com/sign-in-with-barnomala/' . rawurlencode($schoolTenantId !== '' ? $schoolTenantId : 'demo');
    @endphp
    <main class="mx-auto flex min-h-screen w-full max-w-3xl items-center px-6 py-16">
            <section
                class="w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-200/70">
                <div class="h-2 w-full bg-amber-500"></div>
                <div class="space-y-6 p-8 sm:p-10">
                    <p class="text-sm font-semibold uppercase tracking-wider text-amber-600">Authentication Error</p>
                    <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                        {{ $title ?? 'SSO Request Failed' }}</h1>
                    <p class="text-base leading-7 text-slate-600">
                        {{ $message ?? 'Required SSO data was not provided. Please start the login flow again.' }}</p>

                    <div class="flex flex-wrap gap-3 pt-2">
                        <button type="button" onclick="signInWithBarnomala('{{ $portalLoginUrl }}')"
                            class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700">
                            Sign In with Barnomala
                        </button>
                        <button type="button" onclick="window.history.back();"
                            class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            Go Back
                        </button>
                    </div>
                </div>
            </section>
        </main>
    </body>

    <script>
        window.signInWithBarnomala = function(url) {
            const width = 600;
            const height = 700;
            const left = (window.innerWidth - width) / 2;
            const top = (window.innerHeight - height) / 2;

            const popup = window.open(
                url,
                'BarnomalaAuth',
                `width=${width},height=${height},top=${top},left=${left},status=no,menubar=no,toolbar=no,location=no`
            );

            if (window.focus && popup) popup.focus();

            const timer = setInterval(() => {
                if (popup.closed) {
                    clearInterval(timer);
                    // Do not reload automatically, since sso-success.blade.php handles the redirection
                }
            }, 1000);
        };
    </script>
</html>
