<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'primary_domain',
        'domains',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'domains' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function specialists(): HasMany
    {
        return $this->hasMany(Specialist::class);
    }

    public function featuredItems(): HasMany
    {
        return $this->hasMany(FeaturedItem::class);
    }
}


