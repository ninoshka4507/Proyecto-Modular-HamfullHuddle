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
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->integer('tmdb_id');
            $table->string('calificacion', 20); // 'me_gusto', 'neutro', 'no_me_gusto'
            
            $table->unique(['usuario_id', 'tmdb_id'], 'unique_usuario_pelicula');
        });

        Schema::create('vistas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->integer('tmdb_id');
            $table->timestamp('fecha_vista')->useCurrent();

            $table->unique(['usuario_id', 'tmdb_id'], 'unique_vista');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings_and_views_tables');
    }
};
