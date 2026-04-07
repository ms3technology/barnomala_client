<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsPublicPageData;
use App\Models\Notice;
use App\Models\News;
use App\Models\Speech;

class HomeController extends Controller
{
    use BuildsPublicPageData;

    public function index()
    {
        $publicData = $this->getPublicPageData();
        $options = $publicData['options'];
        $navigationItems = $publicData['navigationItems'];

        $notices = Notice::with('artifacts')
            ->where('is_active', true)
            ->orderBy('is_urgent', 'desc')
            ->orderBy('published_at', 'desc')
            ->take(10)
            ->get();

        $speeches = Speech::query()
            ->where('is_active', true)
            ->orderBy('row_index')
            ->orderBy('column_index')
            ->get();

        $featuredNews = News::where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->take(6)
            ->get();

        $galleryNews = News::where('is_active', true)
            ->whereNotNull('image_json')
            ->orderBy('published_at', 'desc')
            ->take(8)
            ->get();

        $sliderImages = $options['institute.branding.slider_json'] ?? [];
        if (is_string($sliderImages)) {
            $sliderImages = json_decode($sliderImages, true) ?: [];
        }

        $teachers = [
            ['teacherid' => 1, 'teacherName' => 'Lead Teacher', 'teacherDesignation' => 'Principal', 'teacherImg' => asset('images/teacher.png')],
            ['teacherid' => 2, 'teacherName' => 'Academic Coordinator', 'teacherDesignation' => 'Vice Principal', 'teacherImg' => asset('images/teacher.png')],
            ['teacherid' => 3, 'teacherName' => 'Senior Teacher', 'teacherDesignation' => 'Department Head', 'teacherImg' => asset('images/teacher.png')],
            ['teacherid' => 4, 'teacherName' => 'Faculty Member', 'teacherDesignation' => 'Senior Instructor', 'teacherImg' => asset('images/teacher.png')],
        ];

        $stats = [
            ['label' => 'Classes', 'count' => $options['institute.stats.classes_count'] ?? $options['totalClasses'] ?? null, 'img' => asset('images/slider-1.png')],
            ['label' => 'Students', 'count' => $options['institute.stats.students_count'] ?? $options['totalStudents'] ?? null, 'img' => asset('images/slider-2.png')],
            ['label' => 'Teachers', 'count' => $options['institute.stats.teachers_count'] ?? $options['totalTeachers'] ?? null, 'img' => asset('images/teacher.png')],
            ['label' => 'Staffs', 'count' => $options['institute.stats.staffs_count'] ?? $options['totalStaffs'] ?? null, 'img' => asset('images/teacher.png')],
        ];

        $quickLinks = [
            ['label' => 'Students', 'url' => '#'],
            ['label' => 'Teachers', 'url' => '#'],
            ['label' => 'Attendance', 'url' => '#'],
            ['label' => 'Result', 'url' => '#'],
            ['label' => 'Routine', 'url' => '#'],
            ['label' => 'Syllabus', 'url' => '#'],
            ['label' => 'Academic Calendar', 'url' => '#'],
            ['label' => 'Photo Gallery', 'url' => '#'],
            ['label' => 'Downloads', 'url' => '#'],
            ['label' => 'News', 'url' => '#'],
            ['label' => 'Notice', 'url' => route('notices.index')],
            ['label' => 'Career', 'url' => '#'],
        ];

        return view('home.index', array_merge($publicData, [
            'navigationItems' => $navigationItems,
            'notices' => $notices,
            'speeches' => $speeches,
            'featuredNews' => $featuredNews,
            'galleryNews' => $galleryNews,
            'sliderImages' => $sliderImages,
            'teachers' => $teachers,
            'stats' => $stats,
            'quickLinks' => $quickLinks,
        ]));
    }
}
