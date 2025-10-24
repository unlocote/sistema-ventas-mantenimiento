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
        Schema::create('tbl_cargo', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('description')->nullable();
        });

        Schema::create('tbl_cargo_rol', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cargo_id')
                ->constrained('tbl_cargo')
                ->onDelete('cascade');

            $table->foreignId('rol_id')
                ->constrained('tbl_rol')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_cargo_rol');
        Schema::dropIfExists('tbl_cargo');
    }
};
