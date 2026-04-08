@extends('layouts.app')

@section('title', 'Home')

@section('content')
    @php
        $stats = [
            ['label' => 'Classes', 'count' => $options['institute.stats.classes_count'] ?? $options['totalClasses'] ?? 0],
            ['label' => 'Students', 'count' => $options['institute.stats.students_count'] ?? $options['totalStudents'] ?? 0],
            ['label' => 'Teachers', 'count' => $options['institute.stats.teachers_count'] ?? $options['totalTeachers'] ?? 0],
            ['label' => 'Staffs', 'count' => $options['institute.stats.staffs_count'] ?? $options['totalStaffs'] ?? 0],
        ];

        $quickLinks = [
            ['label' => 'Students', 'icon' => 'user-graduate', 'color' => 'bg-green-600', 'url' => '#'],
            ['label' => 'Teachers', 'icon' => 'user', 'color' => 'bg-orange-500', 'url' => '#'],
            ['label' => 'Attendance', 'icon' => 'check', 'color' => 'bg-blue-600', 'url' => '#'],
            ['label' => 'Result', 'icon' => 'bolt', 'color' => 'bg-red-600', 'url' => '#'],
            ['label' => 'Routine', 'icon' => 'bell', 'color' => 'bg-green-600', 'url' => '#'],
            ['label' => 'Syllabus', 'icon' => 'book', 'color' => 'bg-orange-500', 'url' => '#'],
            ['label' => 'Academic Calendar', 'icon' => 'calendar', 'color' => 'bg-blue-600', 'url' => '#'],
            ['label' => 'Photo Gallery', 'icon' => 'camera', 'color' => 'bg-red-600', 'url' => '#'],
            ['label' => 'Download', 'icon' => 'download', 'color' => 'bg-green-600', 'url' => '#'],
            ['label' => 'News', 'icon' => 'bell', 'color' => 'bg-orange-500', 'url' => '#'],
            ['label' => 'Notice', 'icon' => 'quote-left', 'color' => 'bg-blue-600', 'url' => '#'],
            ['label' => 'Career', 'icon' => 'briefcase', 'color' => 'bg-red-600', 'url' => '#'],
        ];
    @endphp

    <div x-data="{}">
        @php
            $layout = $options['institute.homepage.layout'] ?? [];
            if (is_string($layout)) {
                $layout = json_decode($layout, true) ?: [];
            }
        @endphp

        @if($layout['latest_news'] ?? true)
            @include('home.partials.latest-news-ticker', ['notices' => $notices])
        @endif

        @if($layout['hero'] ?? true)
            @include('home.partials.hero-slider', [
                'options' => $options,
                'sliderImages' => $sliderImages,
                'notices' => $notices
            ])
        @endif

        @if($layout['institute_info'] ?? true)
            @include('home.partials.institute-info', ['options' => $options])
        @endif

        @if($layout['message_section'] ?? true)
            @include('home.partials.message-section', [
                'options' => $options,
                'speeches' => $speeches
            ])
        @endif

        @if($layout['stats_counter'] ?? true)
            @include('home.partials.stats-counter', ['stats' => $stats])
        @endif

        @if($layout['quick_links'] ?? true)
            @include('home.partials.quick-links', [
                'quickLinks' => $quickLinks,
                'importantLinks' => $importantLinks
                ])
        @endif

        @if($layout['teachers'] ?? true)
            @include('home.partials.teachers', ['teachers' => $teachers])
        @endif

        @if($layout['featured_news'] ?? true)
            @include('home.partials.news-section', ['featuredNews' => $featuredNews ?? []])
        @endif

        @if($layout['gallery'] ?? true)
            @include('home.partials.gallery', ['galleryPhotos' => $galleryPhotos ?? []])
        @endif

        @if($layout['student_demographics'] ?? true)
            @include('home.partials.student-demographics', ['options' => $options])
        @endif
    </div>
@endsection

@push('styles')
<style>
    .font-bn {
        font-family: 'Hind Siliguri', sans-serif;
    }
</style>
@endpush
