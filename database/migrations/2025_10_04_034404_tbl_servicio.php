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
        Schema::create('tbl_servicio', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('tipo');
            $table->tinyInteger('estado');
            $table->timestamp('scheduledTime');
            $table->timestamp('executionTime');
           

            // Clave foránea a Cliente
            $table->foreignId('cliente_id')
                ->constrained('tbl_cliente')  
                ->onDelete('restrict');

            // Clave foránea a DetalleVenta
            $table->foreignId('detalle_venta_id')
                ->constrained('tbl_detalle_venta')  
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_servicio');
    }
};
