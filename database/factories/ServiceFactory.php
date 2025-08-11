<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'tenant_id' => Tenant::factory(),
            'category_id' => function (array $attributes) {
                // Ensure category belongs to the same tenant
                return Category::factory()->state([
                    'tenant_id' => $attributes['tenant_id'] ?? Tenant::factory(),
                ]);
            },
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(),
            'duration_minutes' => $this->faker->randomElement([15, 30, 45, 60, 90, 120]),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'is_active' => true,
        ];
    }
}
