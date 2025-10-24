<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_cliente', function (Blueprint $table) {
            // Agregar columnas si no existen
            if (!Schema::hasColumn('tbl_cliente', 'username')) {
                $table->string('username')->unique()->after('email');
            }

            if (!Schema::hasColumn('tbl_cliente', 'passwordHash')) {
                $table->string('passwordHash')->after('username');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_cliente', function (Blueprint $table) {
            $table->dropColumn(['username', 'passwordHash']);
        });
    }
};
