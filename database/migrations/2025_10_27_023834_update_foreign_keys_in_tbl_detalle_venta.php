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
        Schema::table('tbl_detalle_venta', function (Blueprint $table) {
            // Eliminar la clave foránea y columna venta_id
            $table->dropForeign(['venta_id']);
            $table->dropColumn('venta_id');

            // Re-crear venta_id como obligatoria
            $table->unsignedBigInteger('venta_id')->after('id');
            $table->foreign('venta_id')->references('id')->on('tbl_venta')->onDelete('cascade');

            // Agregar clave foránea a tbl_servicio como obligatoria
            $table->unsignedBigInteger('servicio_id')->after('venta_id');
            $table->foreign('servicio_id')->references('id')->on('tbl_servicio')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_detalle_venta', function (Blueprint $table) {
            $table->dropForeign(['venta_id']);
            $table->dropForeign(['servicio_id']);
            $table->dropColumn(['venta_id', 'servicio_id']);

            $table->unsignedBigInteger('venta_id')->nullable()->after('id');
            $table->foreign('venta_id')->references('id')->on('tbl_venta')->onDelete('cascade');
        });
    }
};
