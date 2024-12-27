<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Level>
 */
class LevelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
                'level' => fake()->word(),
                'icon_color' => fake()->rgbColor(),
                'background_color' => fake()->rgbColor(),
                'active_background_color' => fake()->rgbColor()
        ];
    }
}
