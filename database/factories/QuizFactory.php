<?php

namespace Database\Factories;

use App\Models\Level;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quiz>
 */
class QuizFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'image' => 'https://picsum.photos/200/300?random=' . fake()->uuid,
            'title' => fake()->jobTitle(),
            'description' => fake()->sentence(),
            'duration' => fake()->numberBetween(10, 180),
            'level_id' => 123,
            'total_filled' => fake()->numberBetween(10, 180),
        ];
    }
}
