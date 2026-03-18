<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TMDBService
{
    public static function search($query)
    {
        $response = Http::get(env('TMDB_BASE_URL').'/search/multi',[
            'api_key' => env('TMDB_API_KEY'),
            'query' => $query,
            'language' => 'en-US',
            'include_adult' => false 
        ]);
        return $response->json()['results'] ?? [];
    }

    public static function getDetails($id, $type='movie')
    {
        $endpoint = $type === 'tv' ? 'tv' : 'movie';
        $response = Http::get(env('TMDB_BASE_URL')."/$endpoint/$id",[
            'api_key' => env('TMDB_API_KEY'),
            'language' => 'en-US'
        ]);
        $data = $response->json();

        if(isset($data['success']) && $data['success'] === false){
            $endpoint = $endpoint === 'movie' ? 'tv' : 'movie';
            $response = Http::get(env('TMDB_BASE_URL')."/$endpoint/$id",[
                'api_key' => env('TMDB_API_KEY'),
                'language' => 'en-US'
            ]);
            $data = $response->json();
        }

        $data['display_title'] = $data['title'] ?? $data['name'] ?? $data['original_title'] ?? $data['original_name'] ?? 'Unknown';
        return $data;
    }

    public static function getPopular($type='movie')
    {
        $endpoint = $type === 'tv' ? 'tv' : 'movie';
        $response = Http::get(env('TMDB_BASE_URL')."/$endpoint/popular",[
            'api_key' => env('TMDB_API_KEY'),
            'language' => 'en-US',
            'include_adult' => false 
        ]);
        $results = $response->json()['results'] ?? [];

        foreach($results as &$item){
            $item['display_title'] = $item['title'] ?? $item['name'] ?? $item['original_title'] ?? $item['original_name'] ?? 'Unknown';
        }
        return $results;
    }

    public static function getPopularByGenre($type, $genreId)
    {
        $endpoint = $type === 'tv' ? 'tv' : 'movie';
        
        $response = Http::get(env('TMDB_BASE_URL')."/discover/$endpoint",[
            'api_key' => env('TMDB_API_KEY'),
            'language' => 'en-US',
            'include_adult' => false,
            'with_genres' => $genreId, 
            'sort_by' => 'popularity.desc'
        ]);

        $results = $response->json()['results'] ?? [];

        foreach($results as &$item){
            $item['display_title'] = 
                $item['title'] 
                ?? $item['name'] 
                ?? $item['original_title'] 
                ?? $item['original_name'] 
                ?? 'Unknown';
        }

        return $results;
    }

    public static function getSimilar($id, $type='movie')
    {
        $endpoint = $type === 'tv' ? 'tv' : 'movie';
        $results = [];

        //1. Recomendaciones curadas (Pedimos la página 1 y 2 para tener un "colchón" grande de opciones)
        for ($i = 1; $i <= 2; $i++) {
            $response = Http::get(env('TMDB_BASE_URL')."/$endpoint/$id/recommendations",[
                'api_key'=>env('TMDB_API_KEY'),
                'language'=>'en-US',
                'include_adult'=>false,
                'page' => $i
            ]);
            $results = array_merge($results, $response->json()['results'] ?? []);
        }

        //2. Si las curadas no bastan o los filtros borraron muchas, usamos Discover por géneros
        if (count($results) < 20) {
            $details = self::getDetails($id, $type);
            $genreIds = [];
            if (isset($details['genres'])) {
                foreach ($details['genres'] as $g) {
                    $genreIds[] = $g['id'];
                }
            }
            
            $genreString = implode('|', $genreIds);

            if (!empty($genreString)) {
                $resp2 = Http::get(env('TMDB_BASE_URL')."/discover/$endpoint",[
                    'api_key'=>env('TMDB_API_KEY'),
                    'language'=>'en-US',
                    'include_adult'=>false,
                    'with_genres'=>$genreString, 
                    'sort_by'=>'popularity.desc'
                ]);
                $results = array_merge($results, $resp2->json()['results'] ?? []);
            }
        }

        //Limpieza de duplicados internos por si la API repite elementos
        $uniqueResults = [];
        $seenIds = [];
        foreach($results as $item){
            $itemId = $item['id'] ?? null;
            if($itemId && !in_array($itemId, $seenIds)) {
                $item['display_title'] = $item['title'] ?? $item['name'] ?? $item['original_title'] ?? $item['original_name'] ?? 'Unknown';
                $uniqueResults[] = $item;
                $seenIds[] = $itemId;
            }
        }

        return $uniqueResults;
    }
}