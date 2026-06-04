<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Advisory;

class AdvisoryFactory extends Factory
{
    protected $model = Advisory::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'type' => 'advisory', // Added in the 2026_03_06_003438 migration
            'date' => now()->toDateString(),
        ];
    }
}