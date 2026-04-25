<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Traits\ContentFilter;
use App\Models\Recomendacion;

class HomeController extends Controller
{
    use ContentFilter;

    public function index()
    {
        set_time_limit(120);
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

            $tmdbId = $baseMovie ? $baseMovie->tmdb_id : 0;
            $tipo = $baseMovie ? $baseMovie->tipo : 'movie';

            $script = base_path('ai/recommend.py');
            $command = "py \"$script\" $tmdbId $userId";
            $output = shell_exec($command);

            $lines = [];
            if ($output && trim($output) !== "fallback") {
                $lines = explode("\n", trim($output));
            }

            Recomendacion::where('usuario_id', $userId)->where('origen', 'home')->delete();

            $recomendacionesProcesadas = [];
            $pos = 1;

            foreach($lines as $line) {
                if(!$line) continue;
                $parts = explode("|", $line);
                if(count($parts) < 4) continue;
                
                $recId = (int) $parts[0];
                $score = isset($parts[4]) ? (float) $parts[4] : 0;
                $recTipo = isset($parts[5]) ? trim($parts[5]) : 'movie';

                Recomendacion::create([
                    'usuario_id' => $userId,
                    'busqueda_id' => null,
                    'tmdb_id' => $recId,
                    'posicion' => $pos,
                    'origen' => 'home',    
                    'score' => $score
                ]);

                $recomendacionesProcesadas[] = ['id' => $recId, 'tipo' => $recTipo];
                $pos++;
                
                if($pos > 100) break; 
            }

            $vistasIds = DB::table('vistas')->where('usuario_id', $userId)->pluck('tmdb_id')->toArray();
            $noMeGusto = DB::table('calificaciones')->where('usuario_id', $userId)->where('calificacion', 'no_me_gusto')->pluck('tmdb_id')->toArray();
            $excluir = array_unique(array_merge($vistasIds, $noMeGusto));

            $resultMovies = [];

            foreach($recomendacionesProcesadas as $rec) {
                if(count($resultMovies) >= 100) break; 
                if(in_array($rec['id'], $excluir)) continue;

                $movie = TMDBService::getDetails($rec['id'], $rec['tipo']);
                
                if(!$movie || empty($movie['poster_path'])) continue;
                if(!$this->isSafe($movie)) continue; 

                $movie['media_type'] = $rec['tipo']; 
                $resultMovies[] = $movie;
            }

            /*
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
            */

            return $resultMovies;
        });

        $moviesToShow = collect($cachedMovies)->shuffle()->take(5)->values()->all();

        return view('home', [
            'recommendedMovies' => $moviesToShow,
            'pageTitle' => 'Inicio'
        ]);
    }
}