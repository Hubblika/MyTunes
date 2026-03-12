<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Song>
 */
class SongFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'     => fake()->sentence(3),
            'artist'    => fake()->name(),
            'album'     => fake()->words(2, true),
            'file_name' => fake()->word() . '.mp3',
            'cover_url' => fake()->imageUrl(),
            'date'      => fake()->date(),
            'duration'  => fake()->numberBetween(120, 300),
            'genre'     => fake()->word(),
        ];
    }
}