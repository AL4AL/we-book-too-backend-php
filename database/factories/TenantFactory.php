<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant>
 */
class TenantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->company;
        $primaryDomain = Str::slug($name) . '.example.test';

        return [
            'name' => $name,
            'primary_domain' => $primaryDomain,
            'domains' => [$primaryDomain],
            'settings' => [
                'currency' => 'USD',
            ],
            'is_active' => true,
        ];
    }
}
