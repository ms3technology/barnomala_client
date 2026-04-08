@extends('layouts.admin')

@section('title', 'Data Transfer')

@section('content')
<div class="space-y-6">
    <div class="bg-white shadow-sm rounded-lg border border-slate-200">
        <div class="p-6 border-b border-slate-200">
            <h3 class="text-lg font-semibold text-slate-800">Transfer Speeches</h3>
            <p class="text-sm text-slate-500 mt-1">Review mapped source values from secondary DB, then transfer into options + speeches table.</p>
        </div>

        <div class="p-6 space-y-4">
            @if($speechTransferError)
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ $speechTransferError }}
                </div>
            @elseif($speechTransferPreview)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-slate-500">Primary speeches</p>
                        <p class="text-xl font-bold text-slate-800">{{ $speechTransferPreview['primary_speeches_count'] }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-slate-500">Source keys ready</p>
                        <p class="text-xl font-bold text-slate-800">{{ $speechTransferPreview['source_ready_count'] }} / {{ count($speechTransferPreview['required_source_keys']) }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-slate-500">Transfer candidates</p>
                        <p class="text-xl font-bold text-slate-800">{{ count($speechTransferPreview['candidates']) }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border border-slate-200 rounded-lg overflow-hidden">
                        <thead class="bg-slate-100 text-slate-700">
                            <tr>
                                <th class="px-4 py-2 text-left">Speech</th>
                                <th class="px-4 py-2 text-left">Slot</th>
                                <th class="px-4 py-2 text-left">Source Ready</th>
                                <th class="px-4 py-2 text-left">Already Exists</th>
                                <th class="px-4 py-2 text-left">Up To Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($speechTransferPreview['candidates'] as $candidate)
                                <tr class="border-t border-slate-200">
                                    <td class="px-4 py-2">
                                        <div class="font-semibold text-slate-800">{{ $candidate['title'] }}</div>
                                        <div class="text-xs text-slate-500">{{ $candidate['name'] }}</div>
                                    </td>
                                    <td class="px-4 py-2">Row {{ $candidate['row_index'] }}, Col {{ $candidate['column_index'] }}</td>
                                    <td class="px-4 py-2">{{ $candidate['has_source'] ? 'Yes' : 'No' }}</td>
                                    <td class="px-4 py-2">{{ $candidate['exists'] ? 'Yes' : 'No' }}</td>
                                    <td class="px-4 py-2">{{ $candidate['up_to_date'] ? 'Yes' : 'No' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <form method="POST" action="{{ route('admin.transfer.speeches') }}">
                    @csrf
                    <button type="submit"
                            @disabled(! $speechTransferPreview['can_transfer'])
                            class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-400 disabled:cursor-not-allowed">
                        <i class="fas fa-right-left mr-2"></i>
                        Transfer Speeches
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-slate-200">
        <div class="p-6 border-b border-slate-200">
            <h3 class="text-lg font-semibold text-slate-800">Laravel Export APIs (For Cloud Import)</h3>
            <p class="text-sm text-slate-500 mt-1">This app now exposes export endpoints matching wp-json resource names.</p>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
            @foreach($exportResources as $resource)
                <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                    <span class="font-mono text-slate-700">GET /api/export/barnomala/v1/{{ $resource }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
