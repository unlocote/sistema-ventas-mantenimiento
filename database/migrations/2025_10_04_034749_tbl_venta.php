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
        Schema::create('tbl_venta', function (Blueprint $table) {
            $table->id();
            $table->timestamp('sellTime')->useCurrent();
            $table->integer('quantity');
            $table->double('sellPrice');

            // Clave foránea a FacturaVenta
            $table->foreignId('factura_venta_id')
                ->constrained('tbl_factura_venta')  
                ->onDelete('restrict');
            // Clave foránea a Servicio
            $table->foreignId('servicio_id')
                ->nullable()
                ->constrained('tbl_servicio')  
                ->onDelete('restrict');
            // Clave foránea a Producto
            $table->foreignId('producto_id')
                ->nullable()
                ->constrained('tbl_producto')  
                ->onDelete('restrict');
            // Clave foránea a Lote
            $table->foreignId('lote_id')
                ->nullable()
                ->constrained('tbl_lote')  
                ->onDelete('restrict');
            // Clave foránea a Promocion
            $table->foreignId('promocion_id')
                ->nullable()
                ->constrained('tbl_promocion')  
                ->onDelete('restrict');
            // Clave foránea a DetalleVenta
            $table->foreignId('detalle_venta_id')
                ->nullable()
                ->constrained('tbl_detalle_venta')  
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_venta');
    }
};
