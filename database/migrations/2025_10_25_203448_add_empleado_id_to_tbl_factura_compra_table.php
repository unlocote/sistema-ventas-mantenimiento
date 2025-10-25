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
        Schema::table('tbl_factura_compra', function (Blueprint $table) {
            // Agregar la columna empleado_id como obligatoria
            $table->unsignedBigInteger('empleado_id')->after('proveedor_id');

            // Definir la relación con tbl_empleado
            $table->foreign('empleado_id')
                ->references('id')
                ->on('tbl_empleado')
                ->onDelete('cascade'); // Si se elimina el empleado, se eliminan sus facturas
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_factura_compra', function (Blueprint $table) {
            $table->dropForeign(['empleado_id']);
            $table->dropColumn('empleado_id');
        });
    }
};
