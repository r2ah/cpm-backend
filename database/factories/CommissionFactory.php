<?php

namespace Database\Factories;

use App\Models\Commission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Commission>
 */
class CommissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $commission = Commission::factory()
            ->has(User::factory(3), 'members')
            ->create([
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'level' => fake()->randomElement(['Nacional', 'Provincial', 'Municipal', 'Local']),
            'region' => fake()->latitude() . ',' . fake()->longitude(),
            'parent_id' => null
        ]);

        return $commission->toArray();
    }
}
