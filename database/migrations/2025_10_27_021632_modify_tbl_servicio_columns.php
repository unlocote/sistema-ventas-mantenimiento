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
            // Eliminar columnas existentes
            $table->dropColumn(['executionTime', 'scheduledTime']);

            // Crear columnas nuevamente como nullable
            $table->timestamp('executionTime')->nullable()->after('id');
            $table->timestamp('scheduledTime')->nullable()->after('executionTime');

            // Agregar columna para el técnico (empleado)
            $table->unsignedBigInteger('tecnico_id')->nullable()->after('scheduledTime');

            // Agregar descripción de ejecución
            $table->text('descripcion')->nullable()->after('tecnico_id');

            // Clave foránea
            $table->foreign('tecnico_id')->references('id')->on('tbl_empleado')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_servicio', function (Blueprint $table) {
            // Eliminar columnas nuevas
            $table->dropForeign(['tecnico_id']);
            $table->dropColumn(['executionTime', 'scheduledTime', 'tecnico_id', 'descripcion']);

            // Si quieres, recrear las columnas originales no-nullable
            $table->timestamp('executionTime')->after('id');
            $table->timestamp('scheduledTime')->after('executionTime');
        });
    }
};
