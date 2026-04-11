@if($generalCommitteeMembers->isNotEmpty())
<section class="py-16 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-12">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.35em] text-slate-500">Administration</p>
                <h2 class="mt-4 text-4xl font-black text-slate-950">General Committee</h2>
            </div>
            <a href="{{ route('committees.index') }}" class="text-accent text-sm font-bold hover:underline">View All Committees</a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8">
            @foreach($generalCommitteeMembers as $member)
            <div class="group relative flex flex-col overflow-hidden rounded-2xl bg-slate-50 border border-slate-100 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                <div class="block aspect-square overflow-hidden bg-slate-200">
                    @if($member->photo)
                        <img src="{{ asset('storage/' . $member->photo) }}" 
                             alt="{{ $member->name }}" 
                             class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-slate-200">
                            <i class="fas fa-user text-slate-400 text-6xl"></i>
                        </div>
                    @endif
                </div>
                <div class="flex flex-1 flex-col p-4 text-center">
                    <h3 class="font-black text-slate-900 group-hover:text-accent transition-colors text-sm truncate">
                        {{ $member->name }}
                    </h3>
                    <p class="text-[10px] font-bold text-accent uppercase tracking-wider mt-1">{{ $member->designation }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
