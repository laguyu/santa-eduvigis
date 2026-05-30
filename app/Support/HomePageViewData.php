<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class HomePageViewData
{
    private const PRIMARY_SECTION_ALIASES = [
        'hero' => ['hero', 'inicio'],
        'mass_schedule' => ['mass_schedule', 'horarios', 'misas'],
        'sacraments' => ['sacraments', 'sacramentos'],
        'news' => ['news', 'noticias'],
        'community' => ['community', 'comunidad'],
        'contact' => ['contact', 'contacto'],
    ];

    private const SECTION_IDS = [
        'hero' => 'inicio',
        'mass_schedule' => 'horarios',
        'sacraments' => 'sacramentos',
        'news' => 'noticias',
        'community' => 'comunidad',
        'contact' => 'contacto',
    ];

    private const NAV_LABELS = [
        'mass_schedule' => 'Misas',
        'sacraments' => 'Sacramentos',
        'news' => 'Noticias',
        'community' => 'Comunidad',
        'contact' => 'Contacto',
    ];

    private const KICKERS = [
        'hero' => 'Parroquia catolica',
        'mass_schedule' => 'Celebra la eucaristia',
        'sacraments' => 'Camino sacramental',
        'news' => 'Actualidad parroquial',
        'community' => 'Participa y sirve',
        'contact' => 'Te acompanamos',
    ];

    public function build(Collection $content): array
    {
        $rawSections = [];

        foreach (self::PRIMARY_SECTION_ALIASES as $canonicalKey => $aliases) {
            $rawSections[$canonicalKey] = $this->pickSection($content, $aliases);
        }

        $availableAnchors = $this->availableAnchors($rawSections);

        $sections = [
            'hero' => $this->mapSection(
                $rawSections['hero'],
                'hero',
                $this->normalizeCtaUrl(
                    $rawSections['hero']?->cta_url,
                    $this->pickFallbackAnchor($availableAnchors, ['#contacto', '#horarios', '#sacramentos', '#noticias', '#comunidad', '#inicio'])
                )
            ) ?? $this->fallbackHero(),
            'mass_schedule' => $this->mapSection(
                $rawSections['mass_schedule'],
                'mass_schedule',
                $this->normalizeCtaUrl(
                    $rawSections['mass_schedule']?->cta_url,
                    $this->pickFallbackAnchor($availableAnchors, ['#sacramentos', '#contacto', '#noticias', '#comunidad', '#inicio'])
                ),
                $rawSections['mass_schedule'] ? route('sections.show', ['key' => $rawSections['mass_schedule']->key]) : null
            ),
            'sacraments' => $this->mapSection(
                $rawSections['sacraments'],
                'sacraments',
                $this->normalizeCtaUrl(
                    $rawSections['sacraments']?->cta_url,
                    $this->pickFallbackAnchor($availableAnchors, ['#contacto', '#comunidad', '#inicio'])
                ),
                $rawSections['sacraments'] ? route('sections.show', ['key' => $rawSections['sacraments']->key]) : null
            ),
            'news' => $this->mapSection(
                $rawSections['news'],
                'news',
                $this->normalizeCtaUrl(
                    $rawSections['news']?->cta_url,
                    $this->pickFallbackAnchor($availableAnchors, ['#comunidad', '#contacto', '#inicio'])
                ),
                route('news.index')
            ),
            'community' => $this->mapSection(
                $rawSections['community'],
                'community',
                $this->normalizeCtaUrl(
                    $rawSections['community']?->cta_url,
                    $this->pickFallbackAnchor($availableAnchors, ['#contacto', '#inicio'])
                ),
                $rawSections['community'] ? route('sections.show', ['key' => $rawSections['community']->key]) : null
            ),
            'contact' => $this->mapSection(
                $rawSections['contact'],
                'contact',
                $this->normalizeCtaUrl(
                    $rawSections['contact']?->cta_url,
                    $this->pickFallbackAnchor($availableAnchors, ['#inicio'])
                ),
                $rawSections['contact'] ? route('sections.show', ['key' => $rawSections['contact']->key]) : null
            ),
        ];

        $resolvedPrimaryKeys = collect($rawSections)
            ->filter()
            ->pluck('key')
            ->values()
            ->all();

        $extraSections = $content
            ->reject(fn ($section, $key) => in_array($key, $resolvedPrimaryKeys, true))
            ->map(function ($section) {
                return $this->mapExtraSection($section);
            })
            ->filter()
            ->values()
            ->all();

        return [
            'hero' => $sections['hero'],
            'massSchedule' => $sections['mass_schedule'],
            'sacraments' => $sections['sacraments'],
            'news' => $sections['news'],
            'community' => $sections['community'],
            'contact' => $sections['contact'],
            'navItems' => $this->buildNavItems($sections),
            'heroPanelSections' => $this->buildHeroPanelSections($sections),
            'extraSections' => $extraSections,
            'newsIndexUrl' => route('news.index'),
        ];
    }

    private function pickSection(Collection $content, array $aliases): mixed
    {
        foreach ($aliases as $alias) {
            $section = $content->get($alias);

            if ($section) {
                return $section;
            }
        }

        return null;
    }

    private function availableAnchors(array $rawSections): array
    {
        return array_values(array_filter([
            $rawSections['mass_schedule'] ? '#'.self::SECTION_IDS['mass_schedule'] : null,
            $rawSections['sacraments'] ? '#'.self::SECTION_IDS['sacraments'] : null,
            $rawSections['news'] ? '#'.self::SECTION_IDS['news'] : null,
            $rawSections['community'] ? '#'.self::SECTION_IDS['community'] : null,
            $rawSections['contact'] ? '#'.self::SECTION_IDS['contact'] : null,
            '#'.self::SECTION_IDS['hero'],
        ]));
    }

    private function pickFallbackAnchor(array $availableAnchors, array $preferred): string
    {
        foreach ($preferred as $anchor) {
            if (in_array($anchor, $availableAnchors, true)) {
                return $anchor;
            }
        }

        return '#'.self::SECTION_IDS['hero'];
    }

    private function normalizeCtaUrl(?string $value, string $fallback): string
    {
        $url = trim((string) $value);

        if ($url === '') {
            return $fallback;
        }

        if (
            str_starts_with($url, '#')
            || str_starts_with($url, 'http://')
            || str_starts_with($url, 'https://')
            || str_starts_with($url, 'mailto:')
            || str_starts_with($url, 'tel:')
        ) {
            return $url;
        }

        $internalSections = array_values(self::SECTION_IDS);
        $clean = strtolower(ltrim($url, '/'));

        if (in_array($clean, $internalSections, true)) {
            return '#'.$clean;
        }

        return $url;
    }

    private function mapSection(mixed $section, string $canonicalKey, string $ctaUrl, ?string $detailUrl = null): ?array
    {
        if (! $section) {
            return null;
        }

        $body = (string) ($section->body ?? '');
        $subtitle = trim((string) ($section->subtitle ?? ''));
        $summary = $subtitle !== '' ? $subtitle : Str::limit(strip_tags($body), 60);

        return [
            'key' => (string) $section->key,
            'anchor' => self::SECTION_IDS[$canonicalKey],
            'kicker' => self::KICKERS[$canonicalKey] ?? 'Seccion parroquial',
            'title' => (string) $section->title,
            'subtitle' => $subtitle,
            'body_html' => $this->renderBody($body),
            'body_summary' => Str::limit(strip_tags($body), 260),
            'cta_text' => (string) ($section->cta_text ?? ''),
            'cta_url' => $ctaUrl,
            'use_detail_page' => (bool) ($section->use_detail_page ?? false),
            'detail_url' => $detailUrl,
            'images' => $this->imagesFor($section),
            'highlights' => $this->parseHighlights((string) ($section->highlights ?? '')),
            'panel_summary' => $summary,
        ];
    }

    private function mapExtraSection(mixed $section): ?array
    {
        if (! $section) {
            return null;
        }

        $mapped = $this->mapSection(
            $section,
            'contact',
            $this->normalizeCtaUrl($section->cta_url ?? null, '#contacto'),
            route('sections.show', ['key' => $section->key])
        );

        if (! $mapped) {
            return null;
        }

        $mapped['anchor'] = (string) $section->key;
        $mapped['kicker'] = 'Seccion parroquial';

        return $mapped;
    }

    private function imagesFor(mixed $section): Collection
    {
        $images = $section?->images ?? collect();

        if ($images instanceof Collection) {
            return $images;
        }

        return collect($images);
    }

    private function parseHighlights(string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', $value))
            ->map(fn (?string $item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    }

    private function renderBody(string $value): string
    {
        if ($value === '') {
            return '';
        }

        if (preg_match('/<[^>]+>/', $value)) {
            return $value;
        }

        return nl2br(e($value));
    }

    private function buildHeroPanelSections(array $sections): array
    {
        return collect([$sections['mass_schedule'], $sections['sacraments'], $sections['community']])
            ->filter()
            ->map(fn (array $section) => [
                'title' => $section['title'],
                'summary' => $section['panel_summary'],
            ])
            ->values()
            ->all();
    }

    private function buildNavItems(array $sections): array
    {
        $items = [
            ['href' => '#'.self::SECTION_IDS['hero'], 'label' => 'Inicio'],
        ];

        foreach (['mass_schedule', 'sacraments', 'news', 'community', 'contact'] as $canonicalKey) {
            if (! $sections[$canonicalKey]) {
                continue;
            }

            $items[] = [
                'href' => '#'.$sections[$canonicalKey]['anchor'],
                'label' => self::NAV_LABELS[$canonicalKey],
            ];

            if ($canonicalKey === 'news') {
                $items[] = [
                    'href' => route('news.index'),
                    'label' => 'Ver noticias',
                ];
            }
        }

        if (! collect($items)->contains(fn (array $item) => $item['label'] === 'Ver noticias')) {
            $items[] = [
                'href' => route('news.index'),
                'label' => 'Ver noticias',
            ];
        }

        return $items;
    }

    private function fallbackHero(): array
    {
        $body = 'Encuentra toda la informacion pastoral y participa activamente en la vida de nuestra comunidad.';

        return [
            'key' => 'hero',
            'anchor' => self::SECTION_IDS['hero'],
            'kicker' => self::KICKERS['hero'],
            'title' => 'Parroquia Santa Eduviges',
            'subtitle' => 'Casa de fe, esperanza y caridad.',
            'body_html' => $body,
            'body_summary' => Str::limit($body, 260),
            'cta_text' => '',
            'cta_url' => '#inicio',
            'use_detail_page' => false,
            'detail_url' => null,
            'images' => collect(),
            'highlights' => [],
            'panel_summary' => '',
        ];
    }
}
