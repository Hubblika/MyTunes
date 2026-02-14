<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'username' => substr(fake()->unique()->userName(), 0, 20),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'is_admin' => false,
            'is_searchable' => true,
            'description' => fake()->optional()->sentence(),
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn() => [
            'is_admin' => true,
            'is_searchable' => false,
            'password' => Hash::make(env('ADMIN_PASSWORD', Str::random(24))),
        ]);
    }
}
