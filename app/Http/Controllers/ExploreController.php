<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TMDBService;
use App\Traits\ContentFilter;

class ExploreController extends Controller
{
    use ContentFilter; 

    public function index()
    {
        $movies = array_values(array_filter(TMDBService::getPopular('movie'), [$this, 'isSafe']));
        $tv = array_values(array_filter(TMDBService::getPopular('tv'), [$this, 'isSafe']));

        $pageTitle = 'Buscar';

        return view('explore', compact('movies', 'tv', 'pageTitle'));
    }

    public function search(Request $request)
    {
        $results = TMDBService::search($request->q);
        $filtered = array_values(array_filter($results, [$this, 'isSafe']));
        return response()->json($filtered);
    }

    public function genre($nombre)
    {
        $genresMap = [
            'terror' => ['id' => 27, 'title' => 'Terror y Suspenso'],
            'comedia' => ['id' => 35, 'title' => 'Comedias'],
            'accion' => ['id' => 28, 'title' => 'Acción'],
            'fantasia' => ['id' => 14, 'title' => 'Fantasía'],
            'drama' => ['id' => 18, 'title' => 'Dramas'],
            'documental' => ['id' => 99, 'title' => 'Documentales'],
            'crimen' => ['id' => 80, 'title' => 'Crimen y Misterio'],
            'familiar' => ['id' => 10751, 'title' => 'Para toda la familia'],
        ];

        $genreName = strtolower($nombre);

        if (!array_key_exists($genreName, $genresMap)) {
            abort(404);
        }

        $genreId = $genresMap[$genreName]['id'];
        $displayTitle = $genresMap[$genreName]['title'];
        
        $results = [];
        $page = 1;

        while(count($results) < 24 && $page <= 4) {
            
            $moviesRaw = \App\Services\TMDBService::getPopularByGenre('movie', $genreId, $page);
            $tvRaw = \App\Services\TMDBService::getPopularByGenre('tv', $genreId, $page);
            
            if (empty($moviesRaw) && empty($tvRaw)) break;

            foreach($moviesRaw as $item) {
                if($this->isSafe($item) && !collect($results)->contains('id', $item['id'])) {
                    $item['media_type'] = 'movie';
                    $results[] = $item;
                }
            }

            foreach($tvRaw as $item) {
                if($this->isSafe($item) && !collect($results)->contains('id', $item['id'])) {
                    $item['media_type'] = 'tv';
                    $results[] = $item;
                }
            }

            $page++; 
        }

        usort($results, function($a, $b) {
            return ($b['popularity'] ?? 0) <=> ($a['popularity'] ?? 0);
        });

        $results = array_slice($results, 0, 24);

        $pageTitle = $displayTitle;

        return view('genre_results', compact('results', 'pageTitle', 'displayTitle'));
    }
}