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
        Schema::create('tbl_cliente', function (Blueprint $table) {
            $table->id();
            $table->string('identification', 50);
            $table->string('name', 150);
            $table->string('phoneNumber', 50);
            $table->string('address', 50);
            $table->string('email', 150);
            // Clave foránea a TipoId
            $table->foreignId('tipo_id')
                ->constrained('tbl_tipo_id')  // relaciona con tipo_ids.id
                ->onDelete('restrict');    // evita borrar TipoId si tiene personas
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_cliente');
    }
};
