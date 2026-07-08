@extends('layouts.admin')

@section('title', 'Homepage Layout Settings')

@push('header_actions')
<button type="submit" form="layout-form" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
    <i class="fas fa-save mr-2"></i>
    Save Layout
</button>
@endpush

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900 border-b border-gray-200">
        <h1 class="text-2xl font-semibold mb-6">Homepage Layout Visibility</h1>

        <form id="layout-form" action="{{ route('admin.layout.update') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    @php
                        $sections = [
                            'hero' => 'Top Banner / Hero Slider',
                            'latest_news' => 'Latest News Ticker',
                            'institute_info' => 'Identity (EIIN, Center Code etc)',
                            'speech_section' => 'Speech Section (Principal/Chairman Speeches)',
                            'stats_counter' => 'Statistics Counter',
                            'quick_links' => 'Quick Links & Important Links',
                            'teachers' => 'Teachers Section',
                            'student_demographics' => 'Student Demographics (Charts)',
                            'featured_news' => 'Featured News Section',
                            'gallery' => 'Photo Gallery Section',
                            'general_committee' => 'Committee Section',
                        ];
                    @endphp

                    @foreach($sections as $key => $label)
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200 hover:border-indigo-300 transition-colors">
                            <label for="layout_homepage_{{ $key }}" class="text-slate-700 font-medium cursor-pointer">
                                {{ $label }}
                            </label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       name="layout_homepage_{{ $key }}" 
                                       id="layout_homepage_{{ $key }}" 
                                       value="1" 
                                       class="sr-only peer"
                                       {{ ($layout[$key] ?? true) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
