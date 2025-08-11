<?php

namespace Database\Seeders;

use App\Models\FeaturedItem;
use App\Models\Service;
use App\Models\Specialist;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class FeaturedItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::with(['services', 'specialists'])->get();

        foreach ($tenants as $tenant) {
            $services = $tenant->services->shuffle()->take(3);
            foreach ($services as $service) {
                FeaturedItem::factory()->withService($service->id)->create([
                    'tenant_id' => $tenant->id,
                ]);
            }

            $specialists = $tenant->specialists->shuffle()->take(2);
            foreach ($specialists as $specialist) {
                FeaturedItem::factory()->withSpecialist($specialist->id)->create([
                    'tenant_id' => $tenant->id,
                ]);
            }
        }
    }
}
