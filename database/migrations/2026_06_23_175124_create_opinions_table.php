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
            $table->string('address', 255);
//            $table->geography('location', subtype: 'polygon', srid: 4326);
            $table->unsignedBigInteger('designer_id'); //Persona Natural o Juridica
            $table->unsignedBigInteger('investor_id'); //Persona Natural o Juridica
            $table->unsignedBigInteger('builder_id'); //Persona Natural o Juridica
            $table->text('general_characteristics')->nullable();
            $table->unsignedBigInteger('issuing_company');
            $table->enum('issuing_document_code', ['DUS', 'DO', 'Micro']); //TODO: Valorar si debe ser un nomenclador
            $table->text('considerations')->nullable();
            $table->text('observations')->nullable();
            $table->enum('state', ['Elaborado', 'Revisado', 'Aprobado', 'Denegado'])->default('Elaborado');
            $table->dateTime('date');
            $table->unsignedBigInteger('commission_id');
            $table->unsignedBigInteger('prepared_by'); //Usuario del Sistema
            $table->unsignedBigInteger('reviewed_by'); //Usuario del Sistema
            $table->unsignedBigInteger('approved_by'); //Usuario del Sistema
            $table->timestamps();

            $table->foreign('designer_id')->references('id')->on('people')->onDelete('cascade'); //Persona Natural o Juridica
            $table->foreign('investor_id')->references('id')->on('people')->onDelete('cascade'); //Persona Natural o Juridica
            $table->foreign('builder_id')->references('id')->on('people')->onDelete('cascade'); //Persona Natural o Juridica
            $table->foreign('issuing_company')->references('id')->on('authorities')->onDelete('cascade');  //Autoridad que emite la Incripcion
            $table->foreign('commission_id')->references('id')->on('commissions')->onDelete('cascade');
            $table->foreign('prepared_by')->references('id')->on('users')->onDelete('cascade'); //Usuario del Sistema
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('cascade'); //Usuario del Sistema
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade'); //Usuario del Sistema
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opinions', function(Blueprint $table) {
       	    $table->dropForeign('opinions_designer_id_foreign');
            $table->dropForeign('opinions_investor_id_foreign');
            $table->dropForeign('opinions_builder_id_foreign');

            $table->dropForeign('opinions_issuing_company_foreign');

            $table->dropForeign('opinions_prepared_by_foreign');
            $table->dropForeign('opinions_reviewed_by_foreign');
            $table->dropForeign('opinions_approved_by_foreign');
	    });            

        Schema::dropIfExists('opinions');
    }
};
