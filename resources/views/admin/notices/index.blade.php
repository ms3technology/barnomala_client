@extends('layouts.admin')

@section('title', 'Notices')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800">Notice Management</h1>
    <a href="{{ route('admin.notices.create') }}" class="inline-flex items-center px-4 py-2 bg-accent text-white rounded-lg hover:opacity-90 transition-opacity text-sm font-bold">
        <i class="fas fa-plus mr-2"></i> New Notice
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($notices as $notice)
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
        <div class="p-4">
            <div class="flex items-start justify-between mb-2">
                <h3 class="font-bold text-sm text-gray-900 line-clamp-2">{{ $notice->title }}</h3>
                @if($notice->is_urgent)
                    <span class="inline-flex shrink-0 items-center rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                    </span>
                @endif
            </div>
            
            <div class="flex items-center gap-2 mb-3 text-xs text-gray-500">
                <i class="fas fa-calendar"></i>
                {{ $notice->published_at ? $notice->published_at->format('M d, Y') : 'N/A' }}
            </div>

            @if($notice->artifacts->count())
                <div class="mb-3 flex items-center gap-1 text-xs text-gray-600">
                    <i class="fas fa-paperclip"></i>
                    {{ $notice->artifacts->count() }} file(s)
                </div>
            @endif
            
            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                <div class="flex items-center gap-2">
                    @if($notice->is_active)
                        <span class="inline-flex text-xs font-semibold px-2 py-1 bg-green-100 text-green-800 rounded-full">Live</span>
                    @else
                        <span class="inline-flex text-xs font-semibold px-2 py-1 bg-gray-100 text-gray-800 rounded-full">Draft</span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.notices.edit', $notice) }}" class="text-indigo-600 hover:text-indigo-900 text-sm p-1">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.notices.destroy', $notice) }}" method="POST" class="inline" onsubmit="return confirm('Delete this notice?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm p-1">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-12 bg-gray-50 rounded-lg">
        <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
        <p class="text-gray-500 font-medium">No notices found</p>
    </div>
    @endforelse
</div>

<div class="mt-8">
    {{ $notices->links() }}
</div>
@endsection
