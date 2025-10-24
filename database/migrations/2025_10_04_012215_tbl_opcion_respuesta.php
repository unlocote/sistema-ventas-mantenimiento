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
        Schema::create('tbl_opcion_respuesta', function (Blueprint $table) {
            $table->id();
            $table->string('answer', 500);
            // Clave foránea a Pregunta
            $table->foreignId('pregunta_id')
                ->constrained('tbl_pregunta')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_opcion_respuesta');
    }
};
