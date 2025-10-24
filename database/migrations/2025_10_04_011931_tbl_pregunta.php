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
        Schema::create('tbl_pregunta', function (Blueprint $table) {
            $table->id();
            $table->string('question', 500);
            // Clave foránea a Encuesta
            $table->foreignId('encuesta_id')
                ->constrained('tbl_encuesta')  
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_pregunta');
    }
};
