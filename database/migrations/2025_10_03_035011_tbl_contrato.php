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
        Schema::create('tbl_contrato', function (Blueprint $table) {
            $table->id();
            $table->date('creation_date');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->binary('document');

            // Clave foránea a Empleado
            $table->foreignId('empleado_id')
                ->constrained('tbl_empleado')  // relaciona con tipo_ids.id
                ->onDelete('restrict');    // evita borrar Empleado si tiene Contrato
                
            // Clave foránea a Contrato
            $table->foreignId('cargo_id')
                ->constrained('tbl_cargo')  
                ->onDelete('restrict');    // evita borrar Cargo si tiene Contratos
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_contrato');
    }
};
