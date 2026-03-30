<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Traits\ContentFilter;

class HomeController extends Controller
{
    use ContentFilter;

    public function index()
    {
        $userId = Auth::id();
        $cacheKey = 'home_recommendations_user_' . $userId;

        $cachedMovies = Cache::remember($cacheKey, 43200, function () use ($userId) {
            
            $baseMovie = DB::table('calificaciones')
                ->where('usuario_id', $userId)
                ->where('calificacion', 'me_gusto')
                ->orderBy('id', 'desc') 
                ->first();

            if (!$baseMovie) {
                $baseMovie = DB::table('vistas')
                    ->where('usuario_id', $userId)
                    ->orderBy('id', 'desc')
                    ->first();
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

            $resultMovies = [];

            foreach($recommendedTmdbIds as $recId) {
                if(count($resultMovies) >= 100) break; 
                if(in_array($recId, $excluir)) continue;

                $movie = TMDBService::getDetails($recId, $tipo);
                
                if(!$movie || empty($movie['poster_path'])) continue;
                if(!$this->isSafe($movie)) continue; 

                $movie['media_type'] = $tipo; 
                $resultMovies[] = $movie;
            }

            if (count($resultMovies) < 100) {
                $popular = TMDBService::getPopular('movie');
                
                foreach ($popular as $movie) {
                    if (count($resultMovies) >= 100) break;
                    if (in_array($movie['id'], $excluir)) continue;
                    if (empty($movie['poster_path'])) continue;
                    if(!$this->isSafe($movie)) continue; 
                    
                    if (!collect($resultMovies)->contains('id', $movie['id'])) {
                        $movie['media_type'] = 'movie';
                        $resultMovies[] = $movie;
                    }
                }
            }

            return $resultMovies;
        });

        $moviesToShow = collect($cachedMovies)->shuffle()->take(5)->values()->all();

        return view('home', [
            'recommendedMovies' => $moviesToShow,
            'pageTitle' => 'Inicio'
        ]);
    }
}