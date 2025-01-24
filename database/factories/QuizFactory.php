<?php

namespace Database\Factories;

use App\Models\Level;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        $imageName = Str::random(16) . '.png';
        $imageUrl = 'https://picsum.photos/200/300?random=' . Str::uuid();
        $imageContents = Http::get($imageUrl)->body();
        Storage::disk('public')->put('images/' . $imageName, $imageContents);

        return [
            'image' => 'images/' . $imageName,
            'title' => fake()->jobTitle(),
            'instructions' => fake()->text(),
            'description' => fake()->sentence(),
            'duration' => fake()->numberBetween(10, 180),
            'level_id' => Level::factory(),
            'total_filled' => fake()->numberBetween(10, 180),
        ];
    }
}
