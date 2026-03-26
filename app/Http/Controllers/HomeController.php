<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\ContentFilter;

class HomeController extends Controller
{
    use ContentFilter;

    public function index()
    {
        $userId = Auth::id();

        $baseMovie = DB::table('calificaciones')
            ->where('usuario_id', $userId)->where('calificacion', 'me_gusto')->inRandomOrder()->first();

        if (!$baseMovie) {
            $baseMovie = DB::table('vistas')
                ->where('usuario_id', $userId)->inRandomOrder()->first();
        }

        $tmdbId = $baseMovie ? $baseMovie->tmdb_id : collect([27205, 157336, 155, 557, 120, 680])->random();
        $tipo = $baseMovie ? $baseMovie->tipo : 'movie';

        $script = base_path('ai/recommend.py');
        $command = "py \"$script\" $tmdbId $userId";
        $output = shell_exec($command);

        $lines = [];
        if ($output && trim($output) !== "fallback") {
            $lines = explode("\n", trim($output));
        }

        $recommendedTmdbIds = [];
        foreach($lines as $line) {
            if(!$line) continue;
            $parts = explode("|", $line);
            if(count($parts) < 4) continue;
            $recommendedTmdbIds[] = (int) $parts[0];
        }

        $vistasIds = DB::table('vistas')->where('usuario_id', $userId)->pluck('tmdb_id')->toArray();
        $noMeGusto = DB::table('calificaciones')->where('usuario_id', $userId)->where('calificacion', 'no_me_gusto')->pluck('tmdb_id')->toArray();
        $excluir = array_unique(array_merge($vistasIds, $noMeGusto));

        $movies = [];
        shuffle($recommendedTmdbIds);

        foreach($recommendedTmdbIds as $recId) {
            if(count($movies) >= 5) break; 
            if(in_array($recId, $excluir)) continue;

            $movie = TMDBService::getDetails($recId, $tipo);
            
            if(!$movie || empty($movie['poster_path'])) continue;
            if(!$this->isSafe($movie)) continue; 

            $movie['media_type'] = $tipo; 
            $movies[] = $movie;
        }

        if (count($movies) < 5) {
            $popular = TMDBService::getPopular('movie');
            shuffle($popular); 
            
            foreach ($popular as $movie) {
                if (count($movies) >= 5) break;
                if (in_array($movie['id'], $excluir)) continue;
                if (empty($movie['poster_path'])) continue;
                if(!$this->isSafe($movie)) continue; 
                
                if (!collect($movies)->contains('id', $movie['id'])) {
                    $movie['media_type'] = 'movie';
                    $movies[] = $movie;
                }
            }
        }

        return view('home', [
            'recommendedMovies' => $movies,
            'pageTitle' => 'Inicio'
        ]);
    }
}