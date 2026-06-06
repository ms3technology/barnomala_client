@extends('layouts.app')

@section('title', $committee->name)

@section('content')
<section class="py-16">
    <div class="mx-auto max-w-[90%] px-4 sm:px-6 lg:px-8">
        <!-- Back Navigation -->
        <div class="mb-12">
            <a href="{{ route('committees.index') }}" class="group inline-flex items-center gap-2 text-slate-500 hover:text-accent font-bold text-sm transition-colors">
                <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                Back to Committees
            </a>
        </div>

        <div class="bg-white rounded-4xl p-8 lg:p-12 shadow-sm ring-1 ring-slate-200">
            <div class="mb-16">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest bg-accent/10 text-accent mb-6">
                    {{ $committee->type }}
                </span>
                <h1 class="text-4xl lg:text-5xl font-black text-slate-950 mb-4">{{ $committee->name }}</h1>
                <p class="text-lg font-bold text-slate-500">{{ $committee->session }}</p>
                
                @if($committee->description)
                    <div class="mt-8 prose prose-slate max-w-none text-slate-600 leading-relaxed font-medium">
                        {!! nl2br(e($committee->description)) !!}
                    </div>
                @endif
            </div>

            <div class="pt-16 border-t border-slate-100">
                <h2 class="text-3xl font-black text-slate-950 mb-12">Committee Members</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                    @forelse($committee->members as $member)
                    <div class="flex gap-6 items-start">
                        <div class="relative group shrink-0">
                            <div class="w-16 h-16 lg:w-24 lg:h-24 rounded-2xl overflow-hidden bg-slate-100 shadow-lg ring-4 ring-white">
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
                            <h4 class="text-lg font-black text-slate-900 truncate">{{ $member->name }}</h4>
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
                    <p class="col-span-full text-center text-slate-400 font-bold py-12">No members listed for this committee.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
