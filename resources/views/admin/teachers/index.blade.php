@extends('layouts.admin')

@section('title', 'Teacher Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Teachers</h2>
        <p class="text-sm text-slate-500 mt-1">Manage institutional faculty and staff.</p>
    </div>
    <a href="{{ route('admin.teachers.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-bold shadow-sm">
        <i class="fas fa-plus mr-2"></i> Add New Teacher
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold">
                <tr>
                    <th class="px-6 py-4 uppercase tracking-wider text-xs w-16 text-center">Order</th>
                    <th class="px-6 py-4 uppercase tracking-wider text-xs">Name & Designation</th>
                    <th class="px-6 py-4 uppercase tracking-wider text-xs">Department</th>
                    <th class="px-6 py-4 uppercase tracking-wider text-xs">Contact</th>
                    <th class="px-6 py-4 uppercase tracking-wider text-xs">Status</th>
                    <th class="px-6 py-4 uppercase tracking-wider text-xs text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($teachers as $teacher)
                <tr onclick="window.location='{{ route('admin.teachers.edit', $teacher) }}'" class="hover:bg-indigo-50 transition-colors group cursor-pointer">
                    <td class="px-6 py-4 text-center font-bold text-slate-400">
                        {{ $teacher->priority_index }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($teacher->photo)
                                <img src="{{ asset('storage/' . $teacher->photo) }}" alt="{{ $teacher->teacher_name }}" class="w-10 h-10 rounded-full object-cover border border-slate-200 shrink-0">
                            @else
                                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 shrink-0 font-bold border border-slate-200">
                                    {{ substr($teacher->teacher_name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <p class="font-bold text-slate-800">{{ $teacher->teacher_name }}</p>
                                <p class="text-xs text-slate-500">{{ $teacher->designation }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-slate-600">{{ $teacher->department ?: 'N/A' }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-slate-600 text-xs">{{ $teacher->phone }}</p>
                        <p class="text-slate-400 text-xs">{{ $teacher->email }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @if($teacher->status)
                            <span class="inline-flex text-[10px] font-bold uppercase px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-full">Active</span>
                        @else
                            <span class="inline-flex text-[10px] font-bold uppercase px-2 py-0.5 bg-slate-100 text-slate-600 rounded-full">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                            <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST" class="inline" onsubmit="return confirm('Delete this teacher?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="event.stopPropagation()" class="text-slate-400 hover:text-red-600 transition-colors" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-user-tie text-slate-300 text-4xl mb-4"></i>
                            <p class="font-medium text-slate-600">No teachers found</p>
                            <p class="text-sm mt-1">Get started by adding a teacher.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($teachers->hasPages())
<div class="mt-6 flex justify-end">
    {{ $teachers->links() }}
</div>
@endif
@endsection
