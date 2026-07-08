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
    $parent = Commission::updateOrCreate(
    ['email' => 'info@monumentos.com'],
    [
        'name' => 'Comisión Nacional de Monumentos',
        'level' => 'Nacional',
        'region' => null,
        'parent_id' => null
    ]
);

Commission::updateOrCreate(
    ['email' => 'info@provincial.com'],
    [
        'name' => 'Comisión Provincial de Monumentos',
        'level' => 'Provincial',
        'region' => null,
        'parent_id' => $parent->id
    ]
);
}
}
