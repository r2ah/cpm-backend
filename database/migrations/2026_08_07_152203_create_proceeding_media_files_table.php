<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proceeding_media_files', function (Blueprint $table) {

            $table->id();

            $table->foreignId('proceeding_id')
                ->constrained('proceedings')
                ->cascadeOnDelete();

            $table->foreignId('media_file_id')
                ->constrained('media_files')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique([
                'proceeding_id',
                'media_file_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proceeding_media_files');
    }
};