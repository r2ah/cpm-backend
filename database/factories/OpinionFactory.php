<?php

namespace Database\Factories;

use App\Models\Opinion;
use App\Models\MediaFiles;
use App\Models\Intervention;
use App\Models\Authority;
use App\Models\User;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Opinion>
 */
class OpinionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    
    public function definition(): array
    {
        $opinion = Opinion::factory()
            ->has(MediaFiles::factory(), 'attachedFiles')
            ->has(Intervention::factory(), 'interventions')
            ->create([
                'address' => fake()->address(),
                'location' => fake()->latitude() . ',' . fake()->longitude(),
                'designer_id' => Person::factory(),
                'investor_id' => Person::factory(),
                'builder_id' => Person::factory(),
                'general_characteristics' => fake()->paragraph(),
                'issuing_company' => Authority::factory(),
                'issuing_document_code' => fake()->randomElement(['DUS', 'DO', 'Micro']),
                'considerations' => fake()->paragraph(),
                'observations' => fake()->paragraph(),
                'state' => fake()->randomElement(['Elaborado', 'Revisado', 'Aprobado', 'Denegado']),
                'date' => fake()->dateTime(),
                'prepared_by' => User::factory(),
                'reviewed_by' => null,
                'approved_by' => null,
            ]);

        return $opinion->toArray();
    }
}
