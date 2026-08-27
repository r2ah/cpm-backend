<?php

namespace Database\Seeders;

use App\Models\Appointment;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        Appointment::create([
            'person_id' => 1,
            'commission_id' => 1,
            'date' => '2026-08-25',
            'time' => '09:00',
            'status' => 'pendiente',
        ]);

        Appointment::create([
            'person_id' => 2,
            'commission_id' => 1,
            'date' => '2026-08-25',
            'time' => '10:00',
            'status' => 'confirmada',
        ]);

        Appointment::create([
            'person_id' => 3,
            'commission_id' => 2,
            'date' => '2026-08-26',
            'time' => '09:30',
            'status' => 'pendiente',
        ]);
    }
}