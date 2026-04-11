<?php

return [
    'navigation_items' => [
        ['label' => 'Home', 'route' => 'home', 'children' => []],
        ['label' => 'About', 'url' => '#', 'children' => [
            ['label' => 'Speech', 'route' => 'speeches.index'],
            ['label' => 'History', 'route' => 'history.index'],
            ['label' => 'Achivements', 'route' => 'achievements.index'],
        ]],
        ['label' => 'Academic', 'route' => 'academic.index', 'children' => [
            ['label' => 'Academic Calendar', 'route' => 'academic.calendar'],
            ['label' => 'Academic Rules', 'route' => 'academic.rules'],
            ['label' => 'Class Schedule', 'route' => 'academic.schedule'],
            ['label' => 'Exam Schedule', 'route' => 'academic.exam-schedule'],
            ['label' => 'Teachers', 'route' => 'teachers.index'],
            ['label' => 'Former Teachers', 'route' => 'teachers.former'],
            ['label' => 'Lecturers', 'route' => 'lecturers.index'],
            ['label' => 'Staff', 'route' => 'staff.index'],
            ['label' => 'Former Staff', 'route' => 'staff.former'],
            ['label' => 'Committees', 'route' => 'home'],
        ]],
        ['label' => 'Student', 'route' => 'teachers.index', 'children' => [
            ['label' => 'Tution Fees', 'route' => 'student.tuition-fees'],
            ['label' => 'Our Student', 'route' => 'student.students'],
            ['label' => 'Student Uniform', 'route' => 'student.uniform'],
            ['label' => 'Daily Activities', 'route' => 'student.activities'],
            ['label' => 'Mobile Banking', 'route' => 'student.mobile-banking'],
        ]],
        ['label' => 'Notice', 'route' => 'notices.index', 'children' => []],
        ['label' => 'News', 'route' => 'news.index', 'children' => []],
        ['label' => 'Gallery', 'route' => 'gallery.index', 'children' => []],
        ['label' => 'Contact', 'route' => 'contact.index', 'children' => []],
        // ['label' => 'Result', 'route' => 'results.index', 'children' => []],
        // ['label' => 'Apply Online', 'route' => 'apply.index', 'children' => []],
    ],

    'important_links' => [
        ['title' => 'Sylhet Education Board', 'url' => 'https://www.sylhetboard.gov.bd'],
        ['title' => 'Directorate of Secondary and Higher Education', 'url' => 'https://www.dshe.gov.bd'],
        ['title' => 'Ministry of Education', 'url' => 'https://moedu.gov.bd'],
        ['title' => 'Bangladesh National Portal', 'url' => 'https://bangladesh.gov.bd'],
        ['title' => 'BANBEIS', 'url' => 'https://www.banbeis.gov.bd'],
        ['title' => 'Teachers Portal', 'url' => 'https://www.teachers.gov.bd'],
    ],
];
