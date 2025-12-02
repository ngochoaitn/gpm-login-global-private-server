<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $user = User::where('email', 'Administrator')->first();
        return [
            'name' => fake()->words(2, true),
            'sort_order' => fake()->numberBetween(1, 100),
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ];
    }
}
