<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            ServiceSeeder::class,
            SpecialistSeeder::class,
            FeaturedItemSeeder::class,
        ]);
    }
}
