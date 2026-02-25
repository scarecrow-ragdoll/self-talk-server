<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'       => fake()->words(3, true),
            'type'       => fake()->randomElement(['direct', 'group']),
            'created_by' => User::factory(),
        ];
    }
}
