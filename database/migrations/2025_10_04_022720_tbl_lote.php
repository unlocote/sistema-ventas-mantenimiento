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
        Schema::create('tbl_lote', function (Blueprint $table) {
            $table->id();
            $table->date('buyDate');
            $table->date('expirationDate');
            $table->integer('initialQtty');
            $table->double('buyPrice');
            $table->integer('currentQtty');
            // Clave foránea a Producto
            $table->foreignId('producto_id')
                ->constrained('tbl_producto')  
                ->onDelete('restrict');
            // Clave foránea a Proveedor
            $table->foreignId('proveedor_id')
                ->constrained('tbl_proveedor')  
                ->onDelete('restrict');
            // Clave foránea a DetalleLote
            $table->foreignId('detalle_lote_id')
                ->constrained('tbl_detalle_lote')  
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_lote');
    }
};

