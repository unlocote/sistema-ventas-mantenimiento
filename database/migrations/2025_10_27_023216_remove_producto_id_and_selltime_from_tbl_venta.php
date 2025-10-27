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
        Schema::table('tbl_venta', function (Blueprint $table) {
            // Eliminar clave foránea de producto_id si existe
            $table->dropForeign(['producto_id']);
            // Eliminar columnas
            $table->dropColumn(['producto_id', 'sellTime']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_venta', function (Blueprint $table) {
            $table->unsignedBigInteger('producto_id')->nullable()->after('id');
            $table->foreign('producto_id')->references('id')->on('tbl_producto')->onDelete('cascade');
            $table->timestamp('sellTime')->nullable()->after('producto_id');
        });
    }
};
