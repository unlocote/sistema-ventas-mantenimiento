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
        // 1. Modificar tbl_venta
        Schema::table('tbl_venta', function (Blueprint $table) {
            $table->dropForeign(['detalle_venta_id']); // quitar FK si existe
            $table->dropColumn('detalle_venta_id');    // quitar columna
        });

        // 2. Modificar tbl_detalle_venta para agregar FK a tbl_venta
        Schema::table('tbl_detalle_venta', function (Blueprint $table) {
            $table->unsignedBigInteger('venta_id')->after('id')->nullable();
            $table->foreign('venta_id')->references('id')->on('tbl_venta')->onDelete('cascade');
        });

        // 3. Modificar tbl_servicio para eliminar columna y FK detalle_venta_id
        Schema::table('tbl_servicio', function (Blueprint $table) {
            $table->dropForeign(['detalle_venta_id']); // quitar FK si existe
            $table->dropColumn('detalle_venta_id');    // quitar columna
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. tbl_venta
        Schema::table('tbl_venta', function (Blueprint $table) {
            $table->unsignedBigInteger('detalle_venta_id')->nullable()->after('id');
            $table->foreign('detalle_venta_id')->references('id')->on('tbl_detalle_venta')->onDelete('cascade');
        });

        // 2. tbl_detalle_venta
        Schema::table('tbl_detalle_venta', function (Blueprint $table) {
            $table->dropForeign(['venta_id']);
            $table->dropColumn('venta_id');
        });

        // 3. tbl_servicio
        Schema::table('tbl_servicio', function (Blueprint $table) {
            $table->unsignedBigInteger('detalle_venta_id')->nullable()->after('id');
            $table->foreign('detalle_venta_id')->references('id')->on('tbl_detalle_venta')->onDelete('cascade');
        });
    }
};
