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

            $table->string('ci');
            $table->string('nombre');
            $table->string('apellidos');

            // Datos opcionales del cliente
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();

            $table->foreignId('commission_id')
                ->constrained('commissions')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->date('date');
            $table->time('time');

            $table->string('status')
                ->default('pendiente');

            $table->timestamps();

            // Evita dos citas de la misma comisión
            // en la misma fecha y hora.
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