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
        Schema::table('tbl_factura_venta', function (Blueprint $table) {
            // Eliminar la columna existente
            $table->dropColumn('invoiceNumber');

            // Crear la columna de nuevo como string obligatorio
            $table->string('invoiceNumber', 50)->after('id'); // opcionalmente ajusta la longitud y la posición
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_factura_venta', function (Blueprint $table) {
            // Revertimos: eliminamos la columna string
            $table->dropColumn('invoiceNumber');

            // Creamos la columna original como int (opcional)
            $table->integer('invoiceNumber')->after('id');
        });
    }
};
