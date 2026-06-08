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
        Schema::create('opinions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('entity'); //Entidad
            $table->unsignedBigInteger('designer_id'); //Persona Natural o Juridica
            $table->unsignedBigInteger('investor_id'); //Persona Natural o Juridica
            $table->unsignedBigInteger('builder_id'); //Persona Natural o Juridica
            $table->text('general_characteristics')->nullable();
            $table->unsignedBigInteger('issuing_company'); //Persona Natural o Juridica
            $table->enum('issuing_document_code', ['DUS', 'DO', 'Micro']); //TODO: Valorar si debe ser un nomenclador
            $table->text('considerations')->nullable();
            $table->text('observations')->nullable();
            $table->enum('state', ['Elaborado', 'Revisado', 'Aprobado', 'Denegado'])->default('Elaborado');
            $table->dateTime('date');
            $table->unsignedBigInteger('prepared_by'); //Usuario del Sistema
            $table->unsignedBigInteger('reviewed_by')->nullable(); //Usuario del Sistema
            $table->unsignedBigInteger('approved_by')->nullable(); //Usuario del Sistema

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opinions');
    }
};
