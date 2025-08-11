<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\SpecialistResource;
use App\Domain\Catalog\Entities\Category;
use App\Domain\Catalog\Entities\Service;
use App\Domain\Catalog\Entities\Specialist;
use App\Domain\Catalog\Entities\FeaturedItem;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function services(Request $request)
    {
        $query = Service::query()->with(['media']);
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }
        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($qbuilder) use ($q) {
                $qbuilder->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }
        $services = $query->where('is_active', true)->paginate(15);
        return ServiceResource::collection($services);
    }

    public function categories(Request $request)
    {
        $categories = Category::query()->with(['media'])->paginate(50);
        return CategoryResource::collection($categories);
    }

    public function specialists(Request $request)
    {
        $specialists = Specialist::query()->with(['media'])->where('is_active', true)->paginate(15);
        return SpecialistResource::collection($specialists);
    }

    public function featured(Request $request)
    {
        $featured = FeaturedItem::query()
            ->with(['item', 'item.media'])
            ->orderBy('sort_order')
            ->paginate(20);
        
        return response()->json([
            'data' => $featured->map(function ($item) {
                $resource = match($item->item_type) {
                    'App\\Domain\\Catalog\\Entities\\Service' => new ServiceResource($item->item),
                    'App\\Domain\\Catalog\\Entities\\Category' => new CategoryResource($item->item),
                    'App\\Domain\\Catalog\\Entities\\Specialist' => new SpecialistResource($item->item),
                    default => null,
                };
                return [
                    'type' => class_basename($item->item_type),
                    'data' => $resource,
                ];
            }),
            'meta' => [
                'current_page' => $featured->currentPage(),
                'total' => $featured->total(),
            ]
        ]);
    }
}


