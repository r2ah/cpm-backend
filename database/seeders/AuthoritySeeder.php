<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Authority;

class AuthoritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Authority::create([
            'name' => 'PM'
        ]);

        Authority::create([
            'name' => 'Fenix'
        ]);

        Authority::create([
            'name' => 'CNM'
        ]);

        Authority::create([
            'name' => 'CPM'
        ]);        
    }
}
