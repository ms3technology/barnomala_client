<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsPublicPageData;
use App\Models\News;
use Illuminate\View\View;

class NewsController extends Controller
{
    use BuildsPublicPageData;

    public function index(): View
    {
        $news = News::where('is_active', true)
            ->latest('published_at')
            ->paginate(12);

        return view('news.index', array_merge($this->getPublicPageData(), compact('news')));
    }

    public function show(News $news): View
    {
        if (!$news->is_active) {
            abort(404);
        }

        $news->load('artifacts');
        
        $recentNews = News::where('is_active', true)
            ->where('id', '!=', $news->id)
            ->latest('published_at')
            ->limit(5)
            ->get();

        return view('news.show', array_merge($this->getPublicPageData(), compact('news', 'recentNews')));
    }
}
