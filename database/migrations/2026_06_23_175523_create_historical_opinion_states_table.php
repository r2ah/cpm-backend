<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('historical_opinion_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opinion_id')->constrained('opinions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('date');
            $table->enum('state', ['Elaborado', 'Revisado', 'Aprobado', 'Denegado'])->default('Elaborado');            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historical_opinion_states');
    }
};
