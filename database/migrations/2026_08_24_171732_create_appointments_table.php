<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {

            $table->id();

            // Persona seleccionada para la cita
            $table->foreignId('person_id')
                ->constrained('people')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Comisión seleccionada para la cita
            $table->foreignId('commission_id')
                ->constrained('commissions')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->date('date');

            $table->time('time');

            $table->string('status')
                ->default('pendiente');

            $table->timestamps();

            /*
             * Evita que una misma comisión
             * tenga dos citas en la misma
             * fecha y hora.
             */
            $table->unique([
                'commission_id',
                'date',
                'time'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};