<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('opinions', function (Blueprint $table) {
            $table->string('document_folio')->nullable()->after('issuing_document_code');
        });
    }

    public function down(): void
    {
        Schema::table('opinions', function (Blueprint $table) {
            $table->dropColumn('document_folio');
        });
    }
};
