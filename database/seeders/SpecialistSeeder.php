<?php

namespace Database\Seeders;

use App\Models\Specialist;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class SpecialistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            Specialist::factory(10)->create([
                'tenant_id' => $tenant->id,
            ]);
        }
    }
}
