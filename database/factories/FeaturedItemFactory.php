<?php

namespace Database\Factories;

use App\Models\FeaturedItem;
use App\Models\Service;
use App\Models\Specialist;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FeaturedItem>
 */
class FeaturedItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }

    public function withService(int $serviceId): self
    {
        return $this->state([
            'item_type' => Service::class,
            'item_id' => $serviceId,
        ]);
    }

    public function withSpecialist(int $specialistId): self
    {
        return $this->state([
            'item_type' => Specialist::class,
            'item_id' => $specialistId,
        ]);
    }
}
