<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ParishSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ParishSettingController extends Controller
{
    public function __construct(
        private readonly ParishSettingService $settingService,
    ) {
    }

    public function edit(): View
    {
        return view('admin.settings.edit', [
            'branding' => $this->settingService->getBranding(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'parish_name' => ['required', 'string', 'max:120'],
            'parish_logo' => ['nullable', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
        ]);

        $logoPath = null;

        if ($request->hasFile('parish_logo')) {
            $oldPath = $this->settingService->getLogoPath();
            if ($oldPath) {
                Storage::disk('public')->delete($oldPath);
            }

            $logoPath = $request->file('parish_logo')->store('parish/branding', 'public');
        }

        $this->settingService->updateBranding($validated['parish_name'], $logoPath);

        return redirect()->route('admin.settings.edit')->with('status', 'Configuracion actualizada correctamente.');
    }
}
