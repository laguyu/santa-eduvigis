<?php

namespace App\Services;

use App\Models\ParishSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ParishSettingService
{
    private const CACHE_KEY = 'parish.settings.all';
    private const CACHE_TTL_SECONDS = 600;

    public function getBranding(): array
    {
        $settings = $this->all();
        $logoPath = $settings['parish_logo'] ?? null;

        return [
            'name' => $settings['parish_name'] ?? 'Parroquia Santa Eduviges',
            'logo_path' => $logoPath,
            'logo_url' => $logoPath ? Storage::url($logoPath) : null,
        ];
    }

    public function updateBranding(string $name, ?string $logoPath): void
    {
        if (! Schema::hasTable('parish_settings')) {
            return;
        }

        ParishSetting::query()->updateOrCreate(
            ['key' => 'parish_name'],
            ['value' => $name]
        );

        if ($logoPath !== null) {
            ParishSetting::query()->updateOrCreate(
                ['key' => 'parish_logo'],
                ['value' => $logoPath]
            );
        }

        $this->forgetCache();
    }

    public function getLogoPath(): ?string
    {
        $settings = $this->all();

        return $settings['parish_logo'] ?? null;
    }

    private function all(): array
    {
        if (! Schema::hasTable('parish_settings')) {
            return [];
        }

        return Cache::remember(
            self::CACHE_KEY,
            self::CACHE_TTL_SECONDS,
            static fn () => ParishSetting::query()->pluck('value', 'key')->toArray()
        );
    }

    public function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
