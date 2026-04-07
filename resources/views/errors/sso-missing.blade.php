<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'SSO Error' }} | {{ config('app.name', 'Barnomala') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <main class="mx-auto flex min-h-screen w-full max-w-3xl items-center px-6 py-16">
        <section class="w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-200/70">
            <div class="h-2 w-full bg-amber-500"></div>
            <div class="space-y-6 p-8 sm:p-10">
                <p class="text-sm font-semibold uppercase tracking-wider text-amber-600">Authentication Error</p>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">{{ $title ?? 'SSO Request Failed' }}</h1>
                <p class="text-base leading-7 text-slate-600">{{ $message ?? 'Required SSO data was not provided. Please start the login flow again.' }}</p>

                <div class="flex flex-wrap gap-3 pt-2">
                    <a href="https://cloud.barnomala.com" class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700">
                        Login to Admin Panel
                    </a>
                    <button type="button" onclick="window.history.back();" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Go Back
                    </button>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
