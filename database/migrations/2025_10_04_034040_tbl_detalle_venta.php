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
        Schema::create('tbl_detalle_venta', function (Blueprint $table) {
            $table->id();

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
        Schema::dropIfExists('tbl_detalle_venta');
    }
};
