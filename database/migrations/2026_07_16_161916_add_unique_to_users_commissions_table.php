<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // El índice unique ya es creado por
        // 2026_06_23_182628_create_commissions_users_table.
    }

    public function down(): void
    {
        // No hacer nada.
    }
};