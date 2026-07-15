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

        @include('home.partials.about-section', [
            'options' => $options
        ])

        @if($layout['speech_section'] ?? true)
            @include('home.partials.speech-section', [
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
                'importantLinks' => $importantLinks ?? []
                ])
        @endif

        @if($layout['teachers'] ?? true)
            @include('home.partials.teachers', ['teachers' => $teachers])
        @endif

        @if(($layout['general_committee'] ?? true) && isset($generalCommitteeMembers))
            @include('home.partials.general-committee', ['generalCommitteeMembers' => $generalCommitteeMembers])
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
        font-family: 'Kalpurush', sans-serif;
    }
</style>
@endpush
