<?php

namespace App\Http\Controllers;

use App\Models\NewsPost;
use App\Services\ParishSettingService;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function __construct(
        private readonly ParishSettingService $settingService,
    ) {
    }

    public function index(): View
    {
        return view('news.index', [
            'branding' => $this->settingService->getBranding(),
            'posts' => NewsPost::query()
                ->published()
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->paginate(8),
        ]);
    }

    public function show(string $slug): View
    {
        $post = NewsPost::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('news.show', [
            'branding' => $this->settingService->getBranding(),
            'post' => $post,
        ]);
    }
}
