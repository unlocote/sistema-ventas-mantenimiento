<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_contrato', function (Blueprint $table) {
            // Eliminar la columna binaria existente
            $table->dropColumn('document');
        });

        Schema::table('tbl_contrato', function (Blueprint $table) {
            // Crear una nueva columna string para la ruta
            $table->string('document')->after('end_date');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_contrato', function (Blueprint $table) {
            // Revertir a tipo binario si haces rollback
            $table->dropColumn('document');
        });

        Schema::table('tbl_contrato', function (Blueprint $table) {
            $table->binary('document')->nullable()->after('end_date');
        });
    }
};
