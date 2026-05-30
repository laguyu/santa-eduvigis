<?php

namespace App\Services;

use App\Contracts\Repositories\ParishContentRepositoryInterface;
use App\Models\ParishContent;
use App\Models\ParishContentImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ParishContentService
{
    private const HOME_CACHE_KEY = 'home.parish.contents';
    private const HOME_CACHE_TTL_SECONDS = 300;

    public function __construct(
        private readonly ParishContentRepositoryInterface $repository,
    ) {
    }

    public function getHomeContent(): Collection
    {
        if (! Schema::hasTable('parish_contents')) {
            return $this->defaultHomeContent();
        }

        $cached = Cache::get(self::HOME_CACHE_KEY);

        if ($cached instanceof Collection) {
            return $cached;
        }

        if ($cached !== null) {
            $this->forgetCache();
        }

        $fresh = $this->repository->getActiveForHome()->keyBy('key');

        Cache::put(self::HOME_CACHE_KEY, $fresh, self::HOME_CACHE_TTL_SECONDS);

        return $fresh;
    }

    public function getPaginatedAdminContent(int $perPage = 12): LengthAwarePaginator
    {
        return $this->repository->paginateForAdmin($perPage);
    }

    public function getActiveByKey(string $key): ?ParishContent
    {
        if (! Schema::hasTable('parish_contents')) {
            return null;
        }

        return $this->repository->findActiveByKey($key);
    }

    public function getById(int $id): ParishContent
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): ParishContent
    {
        $images = $data['images'] ?? [];
        unset($data['images']);

        $content = $this->repository->create($data);
        $this->attachImages($content, $images);
        $this->forgetCache();

        return $content;
    }

    public function update(ParishContent $content, array $data): bool
    {
        $images = $data['images'] ?? [];
        $removeImageIds = $data['remove_image_ids'] ?? [];
        unset($data['images'], $data['remove_image_ids']);

        $updated = $this->repository->update($content, $data);

        if ($updated) {
            $this->removeImages($content, $removeImageIds);
            $this->attachImages($content, $images);
            $this->forgetCache();
        }

        return $updated;
    }

    public function delete(ParishContent $content): bool
    {
        $content->loadMissing('images');

        foreach ($content->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $deleted = $this->repository->delete($content);

        if ($deleted) {
            $this->forgetCache();
        }

        return $deleted;
    }

    private function forgetCache(): void
    {
        Cache::forget(self::HOME_CACHE_KEY);
    }

    /**
     * @param array<int, UploadedFile> $images
     */
    private function attachImages(ParishContent $content, array $images): void
    {
        if ($images === []) {
            return;
        }

        $maxOrder = (int) $content->images()->max('display_order');

        foreach ($images as $index => $image) {
            if (! $image instanceof UploadedFile) {
                continue;
            }

            $storedPath = $image->store('parish/albums', 'public');

            $content->images()->create([
                'path' => $storedPath,
                'display_order' => $maxOrder + $index + 1,
            ]);
        }
    }

    /**
     * @param array<int, int|string> $removeImageIds
     */
    private function removeImages(ParishContent $content, array $removeImageIds): void
    {
        if ($removeImageIds === []) {
            return;
        }

        $ids = array_map(static fn ($id): int => (int) $id, $removeImageIds);

        $images = ParishContentImage::query()
            ->where('parish_content_id', $content->id)
            ->whereIn('id', $ids)
            ->get();

        foreach ($images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }
    }

    private function defaultHomeContent(): Collection
    {
        return collect([
            'hero' => (object) [
                'key' => 'hero',
                'title' => 'Parroquia Santa Eduviges',
                'subtitle' => 'Casa de fe, esperanza y caridad.',
                'body' => 'Bienvenidos a nuestra comunidad parroquial.',
                'highlights' => null,
                'cta_text' => 'Contactar parroquia',
                'cta_url' => '#contacto',
                'use_detail_page' => false,
                'images' => collect(),
            ],
            'mass_schedule' => (object) [
                'key' => 'mass_schedule',
                'title' => 'Horarios de Misas',
                'subtitle' => 'Consulta nuestros horarios semanales.',
                'body' => "Lunes a Viernes: 7:00 a.m. y 6:30 p.m.\nSabado: 7:00 a.m. y 6:00 p.m.\nDomingo: 7:00 a.m., 9:00 a.m., 11:00 a.m. y 6:00 p.m.",
                'highlights' => null,
                'cta_text' => 'Ver sacramentos',
                'cta_url' => '#sacramentos',
                'use_detail_page' => false,
                'images' => collect(),
            ],
            'sacraments' => (object) [
                'key' => 'sacraments',
                'title' => 'Sacramentos',
                'subtitle' => 'Acompanamiento pastoral en cada etapa.',
                'body' => "Bautismos, matrimonios y confesiones con formacion previa.",
                'highlights' => null,
                'cta_text' => 'Iniciar proceso',
                'cta_url' => '#contacto',
                'use_detail_page' => false,
                'images' => collect(),
            ],
            'news' => (object) [
                'key' => 'news',
                'title' => 'Noticias Parroquiales',
                'subtitle' => 'Actualidad de nuestra parroquia.',
                'body' => 'Entregamos informacion sobre celebraciones y actividades comunitarias.',
                'highlights' => null,
                'cta_text' => 'Ver comunidad',
                'cta_url' => '#comunidad',
                'use_detail_page' => false,
                'images' => collect(),
            ],
            'community' => (object) [
                'key' => 'community',
                'title' => 'Vida Comunitaria',
                'subtitle' => 'Participa en ministerios y grupos.',
                'body' => 'Pastoral juvenil, catequesis y obras de caridad abiertas a toda la comunidad.',
                'highlights' => "Pastoral juvenil\nCatequesis\nMinisterio de musica\nVoluntariado social",
                'cta_text' => 'Quiero participar',
                'cta_url' => '#contacto',
                'use_detail_page' => false,
                'images' => collect(),
            ],
            'contact' => (object) [
                'key' => 'contact',
                'title' => 'Contacto',
                'subtitle' => 'Estamos para servirte.',
                'body' => "Direccion: Calle Principal, Barrio Central\nTelefono: +57 300 000 0000\nEmail: contacto@santaeduviges.org",
                'highlights' => null,
                'cta_text' => 'Escribir correo',
                'cta_url' => 'mailto:contacto@santaeduviges.org',
                'use_detail_page' => false,
                'images' => collect(),
            ],
        ]);
    }
}
