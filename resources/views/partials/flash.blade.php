<div class="mx-auto max-w-full px-4">
    @if (session('success'))
        <div class="flex items-center gap-3 border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-5 py-4 text-sm font-semibold text-emerald-700 dark:text-emerald-300 shadow-md rounded-xl" role="alert">
            <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-800 rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-check-circle text-emerald-500"></i>
            </div>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 border border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/30 px-5 py-4 text-sm text-rose-700 dark:text-rose-300 shadow-md rounded-xl" role="alert">
            <div class="w-8 h-8 bg-rose-100 dark:bg-rose-800 rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-exclamation-circle text-rose-500"></i>
            </div>
            <span>{{ session('error') }}</span>
        </div>
    @elseif ($errors->any())
        <div class="flex items-center gap-3 border border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/30 px-5 py-4 text-sm text-rose-700 dark:text-rose-300 shadow-md rounded-xl" role="alert">
            <div class="w-8 h-8 bg-rose-100 dark:bg-rose-800 rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-exclamation-triangle text-rose-500"></i>
            </div>
            <div>
                <p class="font-bold">Please fix the highlighted fields.</p>
                <p class="mt-1">{{ $errors->first() }}</p>
            </div>
        </div>
    @endif
</div>
