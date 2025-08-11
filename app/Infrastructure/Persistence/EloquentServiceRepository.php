<?php

namespace App\Infrastructure\Persistence;

use App\Application\Catalog\Contracts\ServiceRepositoryInterface;
use App\Domain\Catalog\Entities\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentServiceRepository implements ServiceRepositoryInterface
{
    public function findById(int $id): ?Service
    {
        return Service::with(['media', 'category'])->find($id);
    }

    public function findActiveServices(?int $categoryId = null, ?string $search = null): LengthAwarePaginator
    {
        $query = Service::query()->with(['media'])->where('is_active', true);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->paginate(15);
    }

    public function findBySlug(string $slug): ?Service
    {
        return Service::with(['media', 'category'])->where('slug', $slug)->first();
    }

    public function create(array $data): Service
    {
        return Service::create($data);
    }

    public function update(Service $service, array $data): Service
    {
        $service->update($data);
        return $service->fresh();
    }

    public function delete(Service $service): bool
    {
        return $service->delete();
    }
}

