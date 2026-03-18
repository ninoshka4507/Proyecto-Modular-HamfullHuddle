<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GeneroSeeder extends Seeder
{
    public function run(): void
    {
        $generos = [
            'Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 
            'Drama', 'Fantasy', 'Horror', 'Mystery', 'Romance', 
            'Sci-Fi', 'Thriller',
            'Reality', 'Documentary', 'Family', 'Music', 'History', 'War', 'Anime'
        ];

        foreach ($generos as $nombre) {
            //Cambiamos a firstOrCreate para evitar duplicados en la base de datos
            \App\Models\Genero::firstOrCreate(['nombre' => $nombre]);
        }
    }
}