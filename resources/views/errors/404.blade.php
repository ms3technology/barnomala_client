<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 Not Found | {{ config('app.name', 'Barnomala') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <main class="relative isolate flex min-h-screen items-center justify-center overflow-hidden px-6 py-16">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_20%_20%,rgba(59,130,246,0.25),transparent_40%),radial-gradient(circle_at_80%_70%,rgba(16,185,129,0.2),transparent_45%),linear-gradient(160deg,#020617,#0f172a)]"></div>

        <section class="w-full max-w-2xl rounded-3xl border border-slate-700/60 bg-slate-900/80 p-8 shadow-2xl shadow-black/40 backdrop-blur sm:p-12">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">Error 404</p>
            <h1 class="mt-4 text-4xl font-extrabold tracking-tight text-white sm:text-5xl">Page Not Found</h1>
            <p class="mt-5 text-base leading-7 text-slate-300 sm:text-lg">The page you are looking for does not exist or may have been moved.</p>

            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-lg bg-cyan-400 px-5 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-cyan-300">
                    Return Home
                </a>
                <button type="button" onclick="window.history.back();" class="inline-flex items-center justify-center rounded-lg border border-slate-600 px-5 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">
                    Previous Page
                </button>
            </div>
        </section>
    </main>
</body>
</html>
