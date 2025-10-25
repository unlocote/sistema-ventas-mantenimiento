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
        Schema::create('tbl_respuesta_encuesta', function (Blueprint $table) {
            $table->id(); // id autoincremental

            // Campos del modelo
            $table->timestamp('assignedAt');
            $table->timestamp('answeredAt')->nullable();
            $table->text('comment')->nullable();

            // Relaciones
            $table->unsignedBigInteger('encuesta_id');
            $table->unsignedBigInteger('cliente_id');

            // Claves foráneas
            $table->foreign('encuesta_id')
                  ->references('id')
                  ->on('tbl_encuesta')   // Ajusta al nombre real de la tabla de encuestas
                  ->onDelete('cascade');

            $table->foreign('cliente_id')
                  ->references('id')
                  ->on('tbl_cliente')    // Ajusta al nombre real de la tabla de clientes
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_respuesta_encuesta');
    }
};
