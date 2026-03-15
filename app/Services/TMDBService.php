<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TMDBService
{

    public static function search($query)
    {

        $response = Http::get(env('TMDB_BASE_URL').'/search/multi',[
            'api_key'=>env('TMDB_API_KEY'),
            'query'=>$query,
            'language'=>'es-MX'
        ]);

        return $response->json()['results'] ?? [];

    }

}