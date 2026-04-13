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
            'Sci-Fi', 'Thriller', 'Reality', 'Documentary', 'Family', 
            'Music', 'History', 'War', 'Anime', 'K-Drama', 'C-Drama',
            'J-Drama', 'True Crime', 'Stand-Up', 'Kids', 'Soap', 'Western',      
            'Talk', 'Sports', 'Indie', 'LGBTQ+', 'Classic'
        ];

        foreach ($generos as $nombre) {
            \App\Models\Genero::firstOrCreate(['nombre' => $nombre]);
        }
    }
}