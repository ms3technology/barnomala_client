@extends('layouts.admin')

@section('title', 'Notice Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <p class="text-sm text-slate-500 mt-1">Manage all public and internal institution notices.</p>
    </div>
    <a href="{{ route('admin.notices.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-bold shadow-sm">
        <i class="fas fa-plus mr-2"></i> New Notice
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold">
                <tr>
                    <th class="px-6 py-4 uppercase tracking-wider text-xs">Title & Status</th>
                    <th class="px-6 py-4 uppercase tracking-wider text-xs">Date</th>
                    <th class="px-6 py-4 uppercase tracking-wider text-xs">Attachments</th>
                    <th class="px-6 py-4 uppercase tracking-wider text-xs text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($notices as $notice)
                <tr onclick="window.location='{{ route('admin.notices.edit', $notice) }}'" class="hover:bg-slate-100 transition-colors group cursor-pointer">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-800 line-clamp-1">{{ $notice->title }}</span>
                            <div class="flex items-center gap-2 mt-1">
                                @if($notice->is_active)
                                    <span class="inline-flex text-[10px] uppercase font-bold px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-full">Live</span>
                                @else
                                    <span class="inline-flex text-[10px] uppercase font-bold px-2 py-0.5 bg-slate-100 text-slate-600 rounded-full">Draft</span>
                                @endif
                                
                                @if($notice->is_urgent)
                                    <span class="inline-flex text-[10px] uppercase font-bold px-2 py-0.5 bg-red-100 text-red-700 rounded-full">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Urgent
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-500">
                        {{ $notice->published_at ? $notice->published_at->format('M d, Y') : 'N/A' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($notice->artifacts->count())
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 text-xs font-medium">
                                <i class="fas fa-paperclip text-slate-400"></i>
                                {{ $notice->artifacts->count() }}
                            </span>
                        @else
                            <span class="text-slate-300">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                            <form action="{{ route('admin.notices.destroy', $notice) }}" method="POST" class="inline" onsubmit="return confirm('Delete this notice?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="event.stopPropagation()" class="text-slate-400 hover:text-red-600 transition-colors" title="Delete">
                                    <i class="fas fa-trash-alt fa-lg"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mb-4">
                                <i class="fas fa-inbox text-2xl text-slate-300"></i>
                            </div>
                            <p class="font-medium text-slate-600">No notices found</p>
                            <p class="text-sm mt-1">Get started by creating a new notice.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($notices->hasPages())
<div class="mt-6 flex justify-end">
    {{ $notices->links() }}
</div>
@endif
@endsection
