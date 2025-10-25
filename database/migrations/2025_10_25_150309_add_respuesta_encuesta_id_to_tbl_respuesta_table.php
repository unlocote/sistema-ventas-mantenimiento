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
        Schema::table('tbl_respuesta', function (Blueprint $table) {
            // Agregamos la columna (ajusta la posición si deseas usar after())
            $table->unsignedBigInteger('respuesta_encuesta_id')->nullable();

            // Creamos la relación con tbl_respuesta_encuesta
            $table->foreign('respuesta_encuesta_id')
                  ->references('id')
                  ->on('tbl_respuesta_encuesta')
                  ->onDelete('cascade'); // o set null / restrict según tu lógica
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_respuesta', function (Blueprint $table) {
            // Primero se elimina la relación
            $table->dropForeign(['respuesta_encuesta_id']);

            // Luego la columna
            $table->dropColumn('respuesta_encuesta_id');
        });
    }
};
