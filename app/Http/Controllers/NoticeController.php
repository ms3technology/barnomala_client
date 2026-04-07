<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsPublicPageData;
use App\Models\Notice;
use Illuminate\View\View;

class NoticeController extends Controller
{
    use BuildsPublicPageData;

    public function index(): View
    {
        $notices = Notice::with('artifacts')
            ->where('is_active', true)
            ->orderBy('is_urgent', 'desc')
            ->orderBy('published_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('notices.index', array_merge($this->getPublicPageData(), [
            'notices' => $notices,
        ]));
    }

    public function show(Notice $notice): View
    {
        abort_unless($notice->is_active, 404);

        $notice->load('artifacts');

        return view('notices.show', array_merge($this->getPublicPageData(), [
            'notice' => $notice,
        ]));
    }
}
