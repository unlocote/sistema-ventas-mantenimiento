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
        Schema::create('tbl_pago_factura_venta', function (Blueprint $table) {
            $table->id();
            $table->timestamp('paidAt')->useCurrent();
            $table->double('paidValue');
            $table->string('paymentMethod', 200);
            $table->text('description')->nullable();
            // Clave foránea a FacturaVenta
            $table->foreignId('factura_venta_id')
                ->constrained('tbl_factura_venta')  
                ->onDelete('restrict');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_factura_compra');
    }
};
