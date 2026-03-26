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

    public static function getPopularByGenre($type, $genreId, $page = 1)
    {
        $endpoint = $type === 'tv' ? 'tv' : 'movie';
        
        $response = Http::get(env('TMDB_BASE_URL')."/discover/$endpoint",[
            'api_key' => env('TMDB_API_KEY'),
            'language' => 'es-MX',
            'include_adult' => false,
            'with_genres' => $genreId, 
            'sort_by' => 'popularity.desc',
            'page' => $page 
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

        for ($i = 1; $i <= 2; $i++) {
            $response = Http::get(env('TMDB_BASE_URL')."/$endpoint/$id/recommendations",[
                'api_key'=>env('TMDB_API_KEY'),
                'language'=>'en-US',
                'include_adult'=>false,
                'page' => $i
            ]);
            $results = array_merge($results, $response->json()['results'] ?? []);
        }

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

    public static function getDetailsFull($id, $type = 'movie')
    {
        $endpoint = $type === 'tv' ? 'tv' : 'movie';

        $response = Http::get(env('TMDB_BASE_URL')."/$endpoint/$id", [
            'api_key' => env('TMDB_API_KEY'),
            'language' => 'es-MX',
            'append_to_response' => 'videos,translations',
            'include_video_language' => 'es-MX,es,en-US,en,null' 
        ]);

        $data = $response->json();

        $creditsPath = $endpoint === 'tv' ? 'aggregate_credits' : 'credits';
        
        $creditsResponse = Http::get(env('TMDB_BASE_URL')."/$endpoint/$id/$creditsPath", [
            'api_key' => env('TMDB_API_KEY'),
            'language' => 'en-US' 
        ]);

        $credits = $creditsResponse->json();

        if (isset($credits['cast'])) {
            $data['credits']['cast'] = array_map(function ($actor) {
                $character = $actor['character'] ?? ($actor['roles'][0]['character'] ?? '');
                
                return [
                    'name' => $actor['name'] ?? '',
                    'character' => $character,
                    'profile_path' => $actor['profile_path'] ?? null
                ];
            }, $credits['cast']);
        }

        if (isset($credits['crew'])) {
            $crewProcessed = [];

            foreach ($credits['crew'] as $person) {
                if (isset($person['jobs'])) {
                    foreach ($person['jobs'] as $job) {
                        $crewProcessed[] = [
                            'name' => $person['name'] ?? '',
                            'job' => $job['job'] ?? '',
                            'episodes' => $job['episode_count'] ?? 0
                        ];
                    }
                } 
                else {
                    $crewProcessed[] = [
                        'name' => $person['name'] ?? '',
                        'job' => $person['job'] ?? '',
                        'episodes' => 1
                    ];
                }
            }

            $data['credits']['crew'] = $crewProcessed;
        }

        $bestOverview = $data['overview'] ?? '';
        $spanishOverview = '';
        $englishOverview = '';

        if (isset($data['translations']['translations'])) {
            foreach ($data['translations']['translations'] as $t) {
                $lang = $t['iso_639_1'] ?? '';
                $text = $t['data']['overview'] ?? '';

                if (empty($text)) continue;

                if ($lang === 'es' && strlen($text) > strlen($spanishOverview)) {
                    $spanishOverview = $text;
                }

                if ($lang === 'en' && strlen($text) > strlen($englishOverview)) {
                    $englishOverview = $text;
                }
            }
        }

        if (strlen($spanishOverview) > 120) {
            $bestOverview = $spanishOverview;
        } elseif (strlen($englishOverview) > 120) {
            $bestOverview = $englishOverview;
        }

        $data['overview_full'] = $bestOverview;
        $data['display_title'] = $data['title'] ?? $data['name'] ?? 'Unknown';

        return $data;
    }
}