<?php

namespace App\Domain\Catalog\Entities;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FeaturedItem extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'item_type', 'item_id', 'sort_order'
    ];

    public function item(): MorphTo
    {
        return $this->morphTo();
    }
}
