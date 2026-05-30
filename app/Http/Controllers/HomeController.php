<?php

namespace App\Http\Controllers;

use App\Services\ParishContentService;
use App\Services\ParishSettingService;
use App\Support\HomePageViewData;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly ParishContentService $parishContentService,
        private readonly ParishSettingService $settingService,
        private readonly HomePageViewData $homePageViewData,
    ) {
    }

    public function index(): View
    {
        $pageData = $this->homePageViewData->build($this->parishContentService->getHomeContent());

        return view('home', [
            'branding' => $this->settingService->getBranding(),
            ...$pageData,
        ]);
    }

    public function showSection(string $key): View
    {
        $section = $this->parishContentService->getActiveByKey($key);

        if (! $section) {
            abort(404);
        }

        return view('section-detail', [
            'section' => $section,
            'branding' => $this->settingService->getBranding(),
        ]);
    }
}
