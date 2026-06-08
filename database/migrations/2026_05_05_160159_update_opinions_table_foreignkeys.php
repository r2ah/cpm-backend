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
        Schema::table('opinions', function(Blueprint $table) {
            $table->foreign('designer_id')
		->references('id')
		->on('people')
		->onUpdate('cascade')
		->onDelete('cascade');

            $table->foreign('investor_id')
		->references('id')
		->on('people')
		->onUpdate('cascade')
		->onDelete('cascade');

            $table->foreign('builder_id')
		->references('id')
		->on('people')
		->onUpdate('cascade')
		->onDelete('cascade');

            $table->foreign('issuing_company')
		->references('id')
		->on('authorities')
		->onUpdate('cascade')
		->onDelete('cascade');

            $table->foreign('prepared_by')
		->references('id')
		->on('users')
		->onUpdate('cascade')
		->onDelete('cascade');

            $table->foreign('reviewed_by')
		->references('id')
		->on('users')
		->onUpdate('cascade')
		->onDelete('cascade');

            $table->foreign('approved_by')
		->references('id')
		->on('users')
		->onUpdate('cascade')
		->onDelete('cascade');
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
    }
};
