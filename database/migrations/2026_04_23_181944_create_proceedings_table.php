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
        Schema::create('proceedings', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->string('address', 255); //Lugar //TODO: Esto debe poder ser la ubicacion geografica donde se realiza la reunion.
            $table->geography('location', subtype: 'polygon', srid: 4326);
            $table->foreign('commission_id')->references('id')->on('commissions')->onDelete('cascade');
            $table->text('agenda'); //Orden del Dia
            $table->text('approaches')->nullable(); //Intervenciones o planteamientos
            $table->text('aggreements')->nullable(); //Acuerdos
            $table->foreign('signed_document')->references('id')->on('media_files')->onDelete('cascade'); //Documento firmado
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proceedings');
    }
};
