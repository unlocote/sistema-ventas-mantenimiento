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
        // Agregar la columna 'brand' a la tabla tbl_lote
        Schema::table('tbl_lote', function (Blueprint $table) {
            $table->string('brand')->after('producto_id');
        });

        // Quitar la columna 'brand' de tbl_detalle_lote
        Schema::table('tbl_detalle_lote', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_detalle_lote', 'brand')) {
                $table->dropColumn('brand');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir los cambios
        Schema::table('tbl_detalle_lote', function (Blueprint $table) {
            $table->string('brand')->nullable();
        });

        Schema::table('tbl_lote', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_lote', 'brand')) {
                $table->dropColumn('brand');
            }
        });
    }
};
