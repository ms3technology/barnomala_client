<div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700 shadow-sm">
            <p class="font-bold">Please fix the highlighted fields.</p>
        </div>
    @endif
</div>
