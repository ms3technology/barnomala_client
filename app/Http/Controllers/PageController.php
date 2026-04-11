<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsPublicPageData;
use App\Models\Speech;
use App\Models\Gallery;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    use BuildsPublicPageData;

    public function about(): View
    {
        return view('pages.about', $this->getPublicPageData());
    }

    public function speeches(): View
    {
        $speeches = Speech::where('is_active', true)
            ->orderBy('row_index', 'asc')
            ->orderBy('column_index', 'asc')
            ->get();
        return view('pages.speeches', array_merge($this->getPublicPageData(), compact('speeches')));
    }

    public function history(): View
    {
        return view('pages.history', $this->getPublicPageData());
    }

    public function gallery(Request $request): View
    {
        $query = Gallery::query();

        if ($request->has('category') && $request->category !== 'All') {
            $query->where('category', $request->category);
        }

        $items = $query->orderBy('date', 'desc')->paginate(12);
        
        $categories = Gallery::whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        return view('pages.gallery', array_merge($this->getPublicPageData(), compact('items', 'categories')));
    }

    public function galleryDetail(Gallery $gallery): View
    {
        return view('pages.gallery-detail', array_merge($this->getPublicPageData(), ['item' => $gallery]));
    }

    public function achievements(): View
    {
        return view('pages.achievements', $this->getPublicPageData());
    }

    public function contact(): View
    {
        return view('pages.contact', $this->getPublicPageData());
    }

    public function academic(): View
    {
        return view('pages.academic', $this->getPublicPageData());
    }

    public function results(): View
    {
        return view('pages.results', $this->getPublicPageData());
    }

    public function teachers(): View
    {
        $teachers = Teacher::where('status', true)
            ->orderBy('priority_index', 'asc')
            ->get();
        return view('pages.teachers', array_merge($this->getPublicPageData(), compact('teachers')));
    }

    public function formerTeachers(): View
    {
        $teachers = Teacher::where('status', false)
            ->orderBy('priority_index', 'asc')
            ->get();
        return view('pages.former-teachers', array_merge($this->getPublicPageData(), compact('teachers')));
    }

    public function teacherDetail(Teacher $teacher): View
    {
        $teacher->load(['qualifications', 'trainings']);
        return view('pages.teacher-detail', array_merge($this->getPublicPageData(), compact('teacher')));
    }

    public function apply(): View
    {
        return view('pages.apply', $this->getPublicPageData());
    }

    public function contactSubmit(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        return back()->with('success', 'Your message has been sent successfully.');
    }
}
