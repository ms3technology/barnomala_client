@extends('layouts.admin')

@section('title', 'Data Transfer')

@section('content')
<div class="space-y-6">
    <div class="bg-white shadow-sm rounded-lg border border-slate-200">
        <div class="p-6 border-b border-slate-200">
            <h3 class="text-lg font-semibold text-slate-800">Transfer About & Speeches</h3>
        </div>

        <div class="p-6 space-y-4">
            @if($speechTransferError)
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ $speechTransferError }}
                </div>
            @elseif($speechTransferPreview)
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
            <h3 class="text-lg font-semibold text-slate-800">Transfer Slider Images</h3>
            <p class="text-sm text-slate-500 mt-1">Transfer images from <code class="bg-slate-100 px-1 rounded">sm_slider_images</code> into <code class="bg-slate-100 px-1 rounded">institute.branding.slider_json</code></p>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('admin.transfer.sliders') }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700">
                    <i class="fas fa-images mr-2"></i>
                    Transfer Sliders
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-slate-200">
        <div class="p-6 border-b border-slate-200">
            <h3 class="text-lg font-semibold text-slate-800">Transfer Gallery Photos</h3>
            <p class="text-sm text-slate-500 mt-1">Import WordPress posts from category <code class="bg-slate-100 px-1 rounded">gallery</code>, set category as <code class="bg-slate-100 px-1 rounded">imported</code>, and upload post images to local storage.</p>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('admin.transfer.galleries') }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-camera mr-2"></i>
                    Transfer Galleries
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-slate-200">
        <div class="p-6 border-b border-slate-200">
            <h3 class="text-lg font-semibold text-slate-800">Transfer Notices</h3>
            <p class="text-sm text-slate-500 mt-1">Import posts from <code class="bg-slate-100 px-1 rounded">latest-notice</code>, download linked files/images as notice artifacts, and save cleaned text content.</p>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('admin.transfer.notices') }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg text-white bg-orange-600 hover:bg-orange-700">
                    <i class="fas fa-bullhorn mr-2"></i>
                    Transfer Notices
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-slate-200">
        <div class="p-6 border-b border-slate-200">
            <h3 class="text-lg font-semibold text-slate-800">Transfer News</h3>
            <p class="text-sm text-slate-500 mt-1">Import posts from <code class="bg-slate-100 px-1 rounded">latest-news</code>, download linked files/images as news artifacts, and save cleaned text content.</p>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('admin.transfer.news') }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg text-white bg-cyan-600 hover:bg-cyan-700">
                    <i class="fas fa-newspaper mr-2"></i>
                    Transfer News
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-slate-200">
        <div class="p-6 border-b border-slate-200">
            <h3 class="text-lg font-semibold text-slate-800">Laravel Export APIs (For Cloud Import)</h3>
            <p class="text-sm text-slate-500 mt-1">This app now exposes export endpoints matching wp-json resource names.</p>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
            @foreach($exportResources as $resource)
                <span class="font-mono text-slate-700">GET /api/barnomala/v1/{{ $resource }}</span>
            @endforeach
        </div>
    </div>
</div>
@endsection
