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
            // Primero eliminamos la restricción de clave foránea
            $table->dropForeign(['cliente_id']);

            // Luego eliminamos la columna
            $table->dropColumn('cliente_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_respuesta', function (Blueprint $table) {
            // Restauramos la columna
            $table->unsignedBigInteger('cliente_id')->nullable();

            // Volvemos a crear la relación (ajusta el nombre de la tabla si difiere)
            $table->foreign('cliente_id')
                  ->references('id')
                  ->on('tbl_cliente')
                  ->onDelete('cascade');
        });
    }
};
