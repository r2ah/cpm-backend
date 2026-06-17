<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Commission;

class CommissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $commision = Commission::create([
            'name' => 'Comisión Nacional de Monumentos',
            'email' => 'info@monumentos.com',
            'level' => 'Nacional',
            'region' => null,
            'parent_id' => null
        ]);

        $commision = Commission::create([
            'name' => 'Comisión Provincial de Monumentos',
            'email' => 'info@monumentos.com',
            'level' => 'Provincial',
            'region' => null,
            'parent_id' => $commision
        ]);
    }
}
