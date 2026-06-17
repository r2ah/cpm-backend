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
        Schema::create('oponion_media_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opinion_id')->constrained('opinions')->onDelete('cascade');
            $table->foreignId('mediafile_id')->constrained('media_files')->onDelete('cascade');
            $table->unsignedBigInteger('opinion_id');
            $table->unsignedBigInteger('mediafile_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oponion_media_files');
    }
};
