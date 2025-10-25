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
        /**
         * TABLA tbl_detalle_lote
         */
        Schema::table('tbl_detalle_lote', function (Blueprint $table) {
            // Agregar columna lote_id
            if (!Schema::hasColumn('tbl_detalle_lote', 'lote_id')) {
                $table->unsignedBigInteger('lote_id')->nullable()->after('id');

                // Clave foránea
                $table->foreign('lote_id')
                      ->references('id')
                      ->on('tbl_lote')
                      ->onDelete('cascade'); // o 'set null' según tu lógica
            }
        });

        /**
         * TABLA tbl_lote
         */

        Schema::table('tbl_lote', function (Blueprint $table) {
            // 1️⃣ Eliminar clave foránea y columna proveedor_id
            if (Schema::hasColumn('tbl_lote', 'proveedor_id')) {
                $table->dropForeign(['proveedor_id']);
                $table->dropColumn('proveedor_id');
            }

            // 2️⃣ Eliminar clave foránea y columna detalle_lote_id
            if (Schema::hasColumn('tbl_lote', 'detalle_lote_id')) {
                $table->dropForeign(['detalle_lote_id']);
                $table->dropColumn('detalle_lote_id');
            }

            // 3️⃣ Agregar columna factura_compra_id con clave foránea
            if (!Schema::hasColumn('tbl_lote', 'factura_compra_id')) {
                $table->unsignedBigInteger('factura_compra_id')->nullable()->after('id');

                $table->foreign('factura_compra_id')
                      ->references('id')
                      ->on('tbl_factura_compra')
                      ->onDelete('cascade'); // o 'set null' según tu lógica
            }
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /**
         * TABLA tbl_lote
         */
        Schema::table('tbl_lote', function (Blueprint $table) {
            // Revertir: eliminar factura_compra_id
            if (Schema::hasColumn('tbl_lote', 'factura_compra_id')) {
                $table->dropForeign(['factura_compra_id']);
                $table->dropColumn('factura_compra_id');
            }

            // Revertir: agregar detalle_lote_id
            if (!Schema::hasColumn('tbl_lote', 'detalle_lote_id')) {
                $table->unsignedBigInteger('detalle_lote_id')->nullable()->after('id');
                $table->foreign('detalle_lote_id')
                      ->references('id')
                      ->on('tbl_detalle_lote')
                      ->onDelete('set null');
            }

            // Revertir: agregar proveedor_id
            if (!Schema::hasColumn('tbl_lote', 'proveedor_id')) {
                $table->unsignedBigInteger('proveedor_id')->nullable()->after('id');
                $table->foreign('proveedor_id')
                      ->references('id')
                      ->on('tbl_proveedor')
                      ->onDelete('set null');
            }
        });

        /**
         * TABLA tbl_detalle_lote
         */
        Schema::table('tbl_detalle_lote', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_detalle_lote', 'lote_id')) {
                $table->dropForeign(['lote_id']);
                $table->dropColumn('lote_id');
            }
        });
    }
};
