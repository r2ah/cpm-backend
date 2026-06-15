<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
			'name' => 'admin',
			'email' => 'admin@cpm.ohc.cu',
			'password' => Hash::make('admin')
		]);

		$admin->assignRole('admin');

        $editor = User::create([
			'name' => 'editor',
			'email' => 'editor@cpm.ohc.cu',
			'password' => Hash::make('editor')
		]);

		$editor->assignRole('editor');

        $editor = User::create([
			'name' => 'Vilma Rodríguez Tápanes',
			'email' => 'vilma.rodriguez@cpm.ohc.cu',
			'password' => Hash::make('vilma.rodriguez')
		]);

		$editor->assignRole('editor');

        $editor = User::create([
			'name' => 'Adriana Galup',
			'email' => 'adriana.galup@cpm.ohc.cu',
			'password' => Hash::make('adriana.galup')
		]);

		$editor->assignRole('editor');
		
        $editor = User::create([
			'name' => 'Elisa Rodríguez Castillo',
			'email' => 'elisa.rodriguez@cpm.ohc.cu',
			'password' => Hash::make('elisa.rodriguez')
		]);

		$editor->assignRole('editor');			
    }
}
