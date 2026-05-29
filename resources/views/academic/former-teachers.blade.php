@extends('layouts.app')

@section('title', 'Former Teachers')

@section('content')
<section class="py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.35em] text-slate-500">Academic History</p>
            <h1 class="mt-4 text-4xl font-black text-slate-950">Our Former Teachers</h1>

            <div class="mt-12">
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-6 gap-8">
                    @forelse($teachers as $teacher)
                    <div class="group relative flex flex-col overflow-hidden rounded-2xl bg-slate-50 border border-slate-100 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                        <a href="{{ route('teachers.show', $teacher->id) }}" class="block aspect-square overflow-hidden bg-slate-200 grayscale hover:grayscale-0 transition-all duration-500">
                            @if($teacher->gender == 'female')
                                <img src="{{ asset('images/female-teacher.png') }}" 
                                    alt="{{ $teacher->teacher_name }}" 
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
                            @elseif($teacher->photo)
                                <img src="{{ $teacher->photo }}" 
                                    alt="{{ $teacher->teacher_name }}" 
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-slate-200">
                                    <i class="fas fa-user-tie text-slate-400 text-6xl"></i>
                                </div>
                            @endif
                        </a>
                        <div class="flex flex-1 flex-col p-2">
                            <h3 class="font-black text-slate-900 group-hover:text-accent transition-colors text-sm">
                                <a href="{{ route('teachers.show', $teacher->id) }}">{{ $teacher->teacher_name }}</a>
                            </h3>
                            <p class="text-[10px] font-bold text-accent uppercase tracking-wider mt-1">{{ $teacher->designation }}</p>
                            @if($teacher->department)
                                <p class="text-[10px] font-semibold text-slate-500 mt-2">{{ $teacher->department }}</p>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-20 text-center bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                        <i class="fas fa-user-graduate text-slate-200 text-6xl mb-6"></i>
                        <p class="text-slate-400 font-bold">No former teachers recorded yet.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
