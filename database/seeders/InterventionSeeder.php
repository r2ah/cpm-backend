<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Intervention;

class InterventionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parent = Intervention::create([
            'name' => 'Uso del Espacio Público',
            'parent_id' => null
        ]);

        Intervention::create([
            'name' => 'Vallas Publicitarias',
            'parent_id' => $parent->id
        ]);

        Intervention::create([
            'name' => 'Mobiliario Urbano',
            'parent_id' => $parent->id
        ]);

        Intervention::create([
            'name' => 'Otros',
            'parent_id' => $parent->id
        ]);
    }
}
