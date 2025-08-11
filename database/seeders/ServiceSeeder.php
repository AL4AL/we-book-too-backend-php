<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Service;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::with('categories')->get();

        foreach ($tenants as $tenant) {
            foreach ($tenant->categories as $category) {
                Service::factory(3)->create([
                    'tenant_id' => $tenant->id,
                    'category_id' => $category->id,
                ]);
            }
        }
    }
}
