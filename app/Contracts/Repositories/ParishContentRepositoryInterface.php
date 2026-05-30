<?php

namespace App\Contracts\Repositories;

use App\Models\ParishContent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ParishContentRepositoryInterface
{
    public function getActiveForHome(): Collection;

    public function findActiveByKey(string $key): ?ParishContent;

    public function paginateForAdmin(int $perPage = 12): LengthAwarePaginator;

    public function findById(int $id): ParishContent;

    public function create(array $data): ParishContent;

    public function update(ParishContent $content, array $data): bool;

    public function delete(ParishContent $content): bool;
}
