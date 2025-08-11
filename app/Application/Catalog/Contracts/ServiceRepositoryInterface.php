<?php

namespace App\Application\Catalog\Contracts;

use App\Domain\Catalog\Entities\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ServiceRepositoryInterface
{
    public function findById(int $id): ?Service;
    public function findActiveServices(?int $categoryId = null, ?string $search = null): LengthAwarePaginator;
    public function findBySlug(string $slug): ?Service;
    public function create(array $data): Service;
    public function update(Service $service, array $data): Service;
    public function delete(Service $service): bool;
}

