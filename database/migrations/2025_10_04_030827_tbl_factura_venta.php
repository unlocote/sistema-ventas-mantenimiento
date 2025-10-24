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
        Schema::create('tbl_factura_venta', function (Blueprint $table) {
            $table->id();
            $table->integer('invoiceNumber');
            $table->timestamp('invoiceCreatedAt')->useCurrent();
            $table->tinyInteger('status');
            // Clave foránea a Cliente
            $table->foreignId('cliente_id')
                ->constrained('tbl_cliente')  
                ->onDelete('restrict');
            // Clave foránea a Empleado (vendedor)
            $table->foreignId('vendedor_id')
                ->constrained('tbl_empleado')  
                ->onDelete('restrict');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_factura_venta');
    }
};
