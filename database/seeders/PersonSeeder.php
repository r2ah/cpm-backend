<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Person;

class PersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Person::create([
            'name' => 'Inmobiliaria Caribe SA',
            'email' => 'info@inmobiliaria-caribe.com',
            'phone' => '123-456-7890',
            'is_natural_person' => false
        ]);

        Person::create([
            'name' => 'Empresa RESTAURA',
            'email' => 'info@restaura.com',
            'phone' => '987-654-3210',
            'is_natural_person' => false
        ]);

        Person::create([
            'name' => 'Empresa Constructora XYZ',
            'email' => 'info@constructora-xyz.com',
            'phone' => '555-123-4567',
            'is_natural_person' => false
        ]);

        Person::create([
            'name' => 'Jhon Doe',
            'email' => 'jhon.doe@example.com',
            'phone' => '555-987-6543',
            'is_natural_person' => true
        ]);
    }
}
