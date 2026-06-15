<?php

namespace Database\Factories;

use App\Models\Proceeding;
use App\Models\User;
use App\Models\MediaFiles;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Proceeding>
 */
class ProceedingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        $proceeding = Proceeding::factory()
            ->has(User::factory(3), 'participants')
            ->create([
                'date' => fake()->dateTime(),
                'address' => fake()->address(),
                'location' => fake()->latitude() . ',' . fake()->longitude(),
                'agenda' => fake()->paragraph(),
                'approaches' => fake()->paragraph(),
                'aggreements' => fake()->paragraph(),
                'signed_document' => MediaFiles::factory()
            ]);

        return $proceeding->toArray();
    }
}
