@extends('layouts.app')

@section('title', 'Committees')

@section('content')
<section class="py-16">
    <div class="mx-auto md:max-w-[70%] px-4 md:px-8">
        <div>
            <h1 class="mt-4 text-4xl font-black text-slate-950">Committee Members</h1>

            @php
                $allMembers = collect();
                foreach ($committees as $committee) {
                    foreach ($committee->members as $member) {
                        $allMembers->push([
                            'member' => $member,
                            'committee' => $committee,
                        ]);
                    }
                }
            @endphp

            <div class="mt-12 space-y-8">
                @forelse($allMembers as $row)
                    @php $member = $row['member']; $committee = $row['committee']; @endphp
                    <div class="group flex flex-col md:flex-row overflow-hidden rounded-2xl bg-white border border-slate-100 transition-all duration-300 hover:shadow-xl">
                        <div class="block w-2/3 md:w-60 mx-auto shrink-0 aspect-square md:aspect-auto overflow-hidden bg-slate-200">
                            @if($member->photo)
                                <img src="{{ asset('storage/' . $member->photo) }}"
                                    alt="{{ $member->name }}"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
                            @else
                                <div class="w-full h-full min-h-52 flex items-center justify-center bg-slate-200">
                                    <i class="fas fa-user text-slate-400 text-6xl"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col p-6">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div>
                                    <h3 class="text-2xl font-black text-slate-900">{{ $member->name }}</h3>
                                    <p class="text-sm font-bold text-accent uppercase tracking-wider mt-1">{{ $member->designation }}</p>
                                    <p class="text-sm font-semibold text-slate-500 mt-1">Member of {{ $committee->name }} <span class="text-slate-400">·</span> {{ $committee->session }}</p>
                                </div>
                            </div>

                            <dl class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-x-8 gap-y-4">
                                @if($member->email)
                                    <div>
                                        <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Email</dt>
                                        <dd class="mt-1 text-sm font-semibold text-slate-700 break-all">{{ $member->email }}</dd>
                                    </div>
                                @endif
                                @if($member->phone)
                                    <div>
                                        <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Phone</dt>
                                        <dd class="mt-1 text-sm font-semibold text-slate-700">{{ $member->phone }}</dd>
                                    </div>
                                @endif
                                @if($member->father_name)
                                    <div>
                                        <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Father's Name</dt>
                                        <dd class="mt-1 text-sm font-semibold text-slate-700">{{ $member->father_name }}</dd>
                                    </div>
                                @endif
                                @if($member->mother_name)
                                    <div>
                                        <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Mother's Name</dt>
                                        <dd class="mt-1 text-sm font-semibold text-slate-700">{{ $member->mother_name }}</dd>
                                    </div>
                                @endif
                                @if($member->joining_date)
                                    <div>
                                        <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Joining Date</dt>
                                        <dd class="mt-1 text-sm font-semibold text-slate-700">{{ $member->joining_date->format('M d, Y') }}</dd>
                                    </div>
                                @endif
                                @if($member->leaving_date)
                                    <div>
                                        <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Leaving Date</dt>
                                        <dd class="mt-1 text-sm font-semibold text-slate-700">{{ $member->leaving_date->format('M d, Y') }}</dd>
                                    </div>
                                @endif
                            </dl>

                            @if($committee->description)
                                <div class="mt-6 pt-6 border-t border-slate-100">
                                    <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400">About the Committee</dt>
                                    <dd class="mt-1 text-sm text-slate-600 leading-relaxed">{{ $committee->description }}</dd>
                                </div>
                            @endif
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
