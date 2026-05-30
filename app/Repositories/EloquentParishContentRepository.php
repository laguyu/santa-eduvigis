<?php

namespace App\Repositories;

use App\Contracts\Repositories\ParishContentRepositoryInterface;
use App\Models\ParishContent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentParishContentRepository implements ParishContentRepositoryInterface
{
    public function getActiveForHome(): Collection
    {
        return ParishContent::query()
            ->select(['id', 'key', 'title', 'subtitle', 'body', 'highlights', 'cta_text', 'cta_url', 'use_detail_page', 'display_order'])
            ->with(['images:id,parish_content_id,path,caption,display_order'])
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();
    }

    public function findActiveByKey(string $key): ?ParishContent
    {
        return ParishContent::query()
            ->with(['images:id,parish_content_id,path,caption,display_order'])
            ->where('is_active', true)
            ->where('key', $key)
            ->first();
    }

    public function paginateForAdmin(int $perPage = 12): LengthAwarePaginator
    {
        return ParishContent::query()
            ->withCount('images')
            ->orderBy('display_order')
            ->paginate($perPage);
    }

    public function findById(int $id): ParishContent
    {
        return ParishContent::query()
            ->with(['images:id,parish_content_id,path,caption,display_order'])
            ->findOrFail($id);
    }

    public function create(array $data): ParishContent
    {
        return ParishContent::query()->create($data);
    }

    public function update(ParishContent $content, array $data): bool
    {
        return $content->update($data);
    }

    public function delete(ParishContent $content): bool
    {
        return (bool) $content->delete();
    }
}
