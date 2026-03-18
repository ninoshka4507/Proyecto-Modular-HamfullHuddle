<?php

namespace App\Http\Controllers;

use App\Models\Busqueda;
use App\Models\Recomendacion;
use App\Services\TMDBService;
use Illuminate\Support\Facades\Http;
use Intervention\Image\ImageManagerStatic as Image;

class RecommendationController extends Controller
{
    public function show($busquedaId)
    {
        $busqueda = Busqueda::findOrFail($busquedaId);
        $userId = auth()->id() ?? 0;
        $tmdbId = $busqueda->tmdb_id;

        //ejecutar IA
        $script = base_path('ai/recommend.py');
        $command = "py \"$script\" $tmdbId $userId";
        $output = shell_exec($command);

        $lines = [];
        
        //FIX ERROR 500: No abortamos. Si falla o devuelve fallback, la lista queda vacía y pasa al Plan B.
        if ($output && trim($output) !== "fallback") {
            $lines = explode("\n", trim($output));
        }

        Recomendacion::where('busqueda_id',$busquedaId)->delete();
        $pos = 1;

        foreach($lines as $line)
        {
            if(!$line) continue;
            $parts = explode("|",$line);
            if(count($parts) < 4) continue;

            $tmdb = (int) $parts[0];

            Recomendacion::create([
                'busqueda_id'=>$busquedaId,
                'tmdb_id'=>$tmdb,
                'posicion'=>$pos
            ]);

            $pos++;
            if($pos > 100) break;
        }

        $original = TMDBService::getDetails($busqueda->tmdb_id, $busqueda->tipo);
        $recs = Recomendacion::where('busqueda_id', $busquedaId)->orderBy('posicion')->get();
        $movies = [];

        foreach($recs as $rec)
        {
            if(count($movies) >= 6) break;

            $movie = TMDBService::getDetails($rec->tmdb_id, $busqueda->tipo);

            if(!$movie || empty($movie['poster_path'])) continue;
            $movies[] = $movie;
        }

        //FALLBACK INTELIGENTE: Si la IA local no encontró 6 resultados buenos.
        if(count($movies) < 6)
        {
            $similar = TMDBService::getSimilar($busqueda->tmdb_id, $busqueda->tipo);

            foreach($similar as $movie)
            {
                if(count($movies) >= 6) break;
                if(empty($movie['poster_path'])) continue;
                
                //ESCUDO LARAVEL: Si TMDB dice que es de adultos, o si tiene muy pocos votos (indicador de basura independiente), ignórala.
                if(($movie['adult'] ?? false) === true) continue;
                if(($movie['vote_count'] ?? 0) < 150) continue;

                $title = $movie['title'] ?? $movie['name'] ?? '';

                if (preg_match('/[\p{Cyrillic}\p{Arabic}\p{Han}\p{Hangul}\p{Hiragana}\p{Katakana}]/u', $title)) {
                    continue; //Se bloquea porque no tiene adaptación oficial al inglés/español
                }
                
                //Evitar duplicados
                if(!collect($movies)->contains('id', $movie['id'] ?? $movie['tmdb_id'] ?? 0)) {
                    $movies[] = $movie;
                }
            }
        }

        if(count($movies) < 6) {
            //Extraemos el ID del primer género de la película original
            $primaryGenre = $original['genres'][0]['id'] ?? null;
            
            if ($primaryGenre) {
                //Buscamos lo más popular, pero ESTRICTAMENTE del mismo género
                $popular = TMDBService::getPopularByGenre($busqueda->tipo, $primaryGenre); 
                
                foreach($popular as $movie) {
                    if(count($movies) >= 6) break; 
                    if(empty($movie['poster_path'])) continue;
                    
                    //Mantenemos la exigencia de calidad
                    if(($movie['adult'] ?? false) === true) continue; 
                    if(($movie['vote_count'] ?? 0) < 150) continue; 

                    //Bloqueamos alfabetos no adaptados para no ver títulos raros
                    $title = $movie['title'] ?? $movie['name'] ?? '';
                    if (preg_match('/[\p{Cyrillic}\p{Arabic}\p{Han}\p{Hangul}\p{Hiragana}\p{Katakana}]/u', $title)) {
                        continue; 
                    }
                    
                    //Validamos que no esté repetida
                    if(!collect($movies)->contains('id', $movie['id'] ?? $movie['tmdb_id'] ?? 0)) {
                        $movies[] = $movie;
                    }
                }
            }
        }

        return view('recommendations',[
            'original'=>$original,
            'movies'=>$movies,
            'busquedaId'=>$busquedaId
        ]);
    }

    public function shareCard($busquedaId)
    {
        $busqueda = Busqueda::findOrFail($busquedaId);

        $original = TMDBService::getDetails($busqueda->tmdb_id,$busqueda->tipo);

        $recs = Recomendacion::where('busqueda_id',$busquedaId)
            ->orderBy('posicion')
            ->limit(6)
            ->get();

        $canvas = Image::canvas(1200,850,'#0f172a');

        $canvas->text("HAMFUHUDDLE",60,60,function($font){
            $font->size(42);
            $font->color('#a855f7');
        });

        $canvas->text("Fans of",60,120,function($font){
            $font->size(30);
            $font->color('#ffffff');
        });

        if(isset($original['poster_path']))
        {
            $poster = Image::make(
                "https://image.tmdb.org/t/p/w200".$original['poster_path']
            )->resize(120,180);

            $canvas->insert($poster,'top-left',60,150);
        }

        $title = $original['display_title'] ?? $original['title'] ?? $original['name'] ?? "Unknown";

        $year = substr(
            $original['release_date'] ??
            $original['first_air_date'] ??
            "",
            0,
            4
        );

        $canvas->text("$title ($year)",200,230,function($font){
            $font->size(32);
            $font->color('#ffffff');
        });

        $canvas->text("should watch:",60,380,function($font){
            $font->size(30);
            $font->color('#ffffff');
        });

        $x = 80;
        $y = 420;
        $count = 0;

        foreach($recs as $rec)
        {
            $movie = TMDBService::getDetails($rec->tmdb_id,$busqueda->tipo);

            if(!$movie) continue;
            if(empty($movie['poster_path'])) continue;

            $poster = Image::make(
                "https://image.tmdb.org/t/p/w300".$movie['poster_path']
            )->resize(200,300);

            $canvas->insert($poster,'top-left',$x,$y);

            $name = $movie['display_title'] ?? $movie['title'] ?? $movie['name'] ?? "Sin título";

            $canvas->text($name,$x,$y + 330,function($font){
                $font->size(20);
                $font->color('#ffffff');
            });

            $x += 360;
            $count++;

            if($count == 3){
                $x = 80;
                $y = 650;
            }
        }

        return $canvas->response('png');
    }
}