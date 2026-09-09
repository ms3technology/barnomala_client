@extends('layouts.app')

@section('title', 'Committees')

@section('content')
<section class="py-0">
    <div class="mx-auto w-full md:max-w-[70%] px-4 sm:px-6 lg:px-8">
        <div>
            {{-- <p class="text-sm font-semibold uppercase tracking-[0.35em] text-slate-500">Administration</p>
            <h1 class="mt-4 text-4xl font-black text-slate-950">Our Committees</h1> --}}

            <div class="mt-12 space-y-12">
                @forelse($committees as $committee)
                <div class="bg-white border border-slate-200 rounded-3xl p-8 lg:p-12 shadow-sm ring-1 ring-slate-200">
                    <div class="mb-10">
                        {{-- <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-accent/10 text-accent mb-4">
                            {{ $committee->type }}
                        </span> --}}
                        <h2 class="text-3xl font-black text-slate-950 mb-2">{{ $committee->name }}</h2>
                        <p class="text-sm font-bold text-slate-500">{{ $committee->session }}</p>
                        @if($committee->description)
                            <p class="mt-6 text-sm text-slate-600 leading-relaxed max-w-3xl">{{ $committee->description }}</p>
                        @endif
                    </div>

                    <div class="pt-10 border-t border-slate-100">
                        <h3 class="text-xl font-black text-slate-950 mb-8">Committee Members</h3>
                        <div class="grid grid-cols-1 gap-12">
                            @forelse($committee->members as $member)
                            <div class="flex gap-6 items-start">
                                <div class="relative group shrink-0">
                                    <div class="w-16 h-16 md:w-40 md:h-40 rounded-2xl overflow-hidden bg-slate-100 shadow-lg ring-4 ring-white">
                                        @if($member->photo)
                                            <img src="{{ asset('storage/' . $member->photo) }}"
                                                 alt="{{ $member->name }}"
                                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-slate-200">
                                                <i class="fas fa-user text-slate-300 text-3xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-lg font-black text-slate-900">{{ $member->name }}</h4>
                                    <p class="text-xs font-bold text-accent uppercase tracking-widest mt-1">{{ $member->designation }}</p>
                                    <div class="mt-4 space-y-2">
                                        @if($member->phone)
                                            <p class="text-xs font-bold text-slate-500 flex items-center gap-2">
                                                <i class="fas fa-phone-alt text-[10px] text-slate-300"></i>
                                                {{ $member->phone }}
                                            </p>
                                        @endif
                                        @if($member->email)
                                            <p class="text-xs font-bold text-slate-500 flex items-center gap-2 truncate">
                                                <i class="fas fa-envelope text-[10px] text-slate-300"></i>
                                                {{ $member->email }}
                                            </p>
                                        @endif
                                        @if($member->father_name)
                                            <p class="text-[11px] font-medium text-slate-400 mt-2">
                                                Father: {{ $member->father_name }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="col-span-full text-center text-slate-400 font-bold py-6">No members listed for this committee.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-20 text-center bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                    <i class="fas fa-users-cog text-slate-200 text-6xl mb-6"></i>
                    <p class="text-slate-400 font-bold">No committees recorded yet.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection
