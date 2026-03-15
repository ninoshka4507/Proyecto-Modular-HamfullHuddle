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
        Schema::create('busquedas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->integer('tmdb_id');
            $table->timestamp('fecha_busqueda')->useCurrent();
        });

        Schema::create('recomendaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('busqueda_id')->nullable()->constrained('busquedas')->onDelete('cascade');
            $table->integer('tmdb_id');
            $table->integer('posicion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('searches_and_recommendations_tables');
    }
};
