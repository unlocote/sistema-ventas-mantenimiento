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
        Schema::table('tbl_servicio', function (Blueprint $table) {
            // Nueva columna tipo_servicio_id
            $table->unsignedBigInteger('tipo_servicio_id')->after('id');

            // Agregar clave foránea
            $table->foreign('tipo_servicio_id')
                ->references('id')
                ->on('tbl_tipo_servicio')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            // Opcional: si quieres eliminar el campo anterior "tipo"
            if (Schema::hasColumn('tbl_servicio', 'tipo')) {
                $table->dropColumn('tipo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_servicio', function (Blueprint $table) {
            // Revertir cambios
            if (Schema::hasColumn('tbl_servicio', 'tipo_servicio_id')) {
                $table->dropForeign(['tipo_servicio_id']);
                $table->dropColumn('tipo_servicio_id');
            }

            // Restaurar campo tipo (opcional)
            $table->integer('tipo')->nullable();
        });
    }
};
