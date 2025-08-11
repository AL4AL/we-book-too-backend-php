<?php

namespace App\Domain\Catalog\Entities;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'mediable_type', 'mediable_id', 'url', 'type', 'alt', 'sort_order', 'meta'
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}


