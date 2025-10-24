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
        Schema::create('tbl_promocion', function (Blueprint $table) {
            $table->id();
            $table->double('discount');
            $table->timestamp('validSince');
            $table->timestamp('validUntil');
            $table->text('description')->nullable();
            // Clave foránea a Lote
            $table->foreignId('lote_id')
                ->constrained('tbl_lote')  
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_promocion');
    }
};
