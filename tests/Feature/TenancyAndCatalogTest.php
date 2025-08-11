<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Domain\Tenancy\Entities\Tenant;
use App\Domain\Catalog\Entities\Category;
use App\Domain\Catalog\Entities\Service;

it('resolves tenant by Host and scopes catalog', function () {
    $tenantA = Tenant::query()->create([
        'name' => 'Tenant A',
        'primary_domain' => 'a.test',
        'domains' => ['a.test'],
        'settings' => [],
        'is_active' => true,
    ]);
    $tenantB = Tenant::query()->create([
        'name' => 'Tenant B',
        'primary_domain' => 'b.test',
        'domains' => ['b.test'],
        'settings' => [],
        'is_active' => true,
    ]);

    $catA = Category::query()->create([
        'tenant_id' => $tenantA->id,
        'name' => 'Hair',
        'slug' => 'hair',
        'description' => 'Hair services',
    ]);
    Service::query()->create([
        'tenant_id' => $tenantA->id,
        'category_id' => $catA->id,
        'name' => 'Cut',
        'slug' => 'cut',
        'description' => 'Hair cut',
        'price' => 10.00,
        'duration_minutes' => 30,
        'is_active' => true,
    ]);

    $catB = Category::query()->create([
        'tenant_id' => $tenantB->id,
        'name' => 'Spa',
        'slug' => 'spa',
        'description' => 'Spa services',
    ]);
    Service::query()->create([
        'tenant_id' => $tenantB->id,
        'category_id' => $catB->id,
        'name' => 'Massage',
        'slug' => 'massage',
        'description' => 'Massage',
        'price' => 20.00,
        'duration_minutes' => 60,
        'is_active' => true,
    ]);

    $respA = $this->withHeaders(['Host' => 'a.test'])
        ->getJson('/api/v1/catalog/services');
    $respA->assertOk();
    $respA->assertJsonPath('data.0.name', 'Cut');

    $respB = $this->withHeaders(['Host' => 'b.test'])
        ->getJson('/api/v1/catalog/services');
    $respB->assertOk();
    $respB->assertJsonPath('data.0.name', 'Massage');
});

it('returns 404 for unknown tenant', function() {
    $response = $this->withHeaders(['Host' => 'unknown.test'])
        ->getJson('/api/v1/catalog/services');
    $response->assertNotFound();
});

it('returns 403 for inactive tenant', function() {
    // Create inactive tenant
    $tenant = Tenant::query()->create([
        'name' => 'Inactive Tenant',
        'primary_domain' => 'inactive.test',
        'domains' => ['inactive.test'],
        'settings' => [],
        'is_active' => false,
    ]);

    $response = $this->withHeaders(['Host' => 'inactive.test'])
        ->getJson('/api/v1/catalog/services');
    $response->assertForbidden();
});

it('filters inactive services', function() {
    $tenant = Tenant::query()->create([
        'name' => 'Filter Tenant',
        'primary_domain' => 'filter.test',
        'domains' => ['filter.test'],
        'settings' => [],
        'is_active' => true,
    ]);

    $category = Category::query()->create([
        'tenant_id' => $tenant->id,
        'name' => 'Mixed',
        'slug' => 'mixed',
        'description' => 'Mixed services',
    ]);

    // Active service
    Service::query()->create([
        'tenant_id' => $tenant->id,
        'category_id' => $category->id,
        'name' => 'Active Service',
        'slug' => 'active-service',
        'description' => 'Active service',
        'price' => 10.00,
        'duration_minutes' => 30,
        'is_active' => true,
    ]);

    // Inactive service
    Service::query()->create([
        'tenant_id' => $tenant->id,
        'category_id' => $category->id,
        'name' => 'Inactive Service',
        'slug' => 'inactive-service',
        'description' => 'Inactive service',
        'price' => 15.00,
        'duration_minutes' => 45,
        'is_active' => false,
    ]);

    $response = $this->withHeaders(['Host' => 'filter.test'])
        ->getJson('/api/v1/catalog/services');
    $response->assertOk();
    
    // Should only return active service
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.name', 'Active Service');
    $response->assertJsonMissing(['name' => 'Inactive Service']);
});



