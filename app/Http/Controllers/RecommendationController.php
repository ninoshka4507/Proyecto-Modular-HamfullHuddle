<?php

namespace App\Http\Controllers;

use App\Models\Busqueda;
use App\Models\Recomendacion;
use App\Services\TMDBService;
use Illuminate\Support\Facades\Http;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

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

        $manager = new ImageManager(new Driver());

        //FONDO CLARO
        $canvas = $manager->create(1200, 1600)->fill('#a5b4fc');

        //TITULO GRANDE
        $canvas->text("Porque viste", 600, 120, function($font){
            $font->size(70);
            $font->color('#1e3a8a');
            $font->align('center');
        });

        //POSTER PRINCIPAL
        if (!empty($original['poster_path'])) {
            try {
                $res = Http::get("https://image.tmdb.org/t/p/w300".$original['poster_path']);
                if ($res->successful()) {
                    $poster = $manager->read($res->body())->resize(250, 380);
                    $canvas->place($poster, 'top-left', 150, 200);
                }
            } catch (\Exception $e) {}
        }

        $title = $original['title'] ?? $original['name'] ?? "Unknown";
        $year = substr($original['release_date'] ?? $original['first_air_date'] ?? "",0,4);
        $rating = number_format($original['vote_average'] ?? 0,1);

        //INFO PRINCIPAL
        $canvas->text($title, 700, 320, function($font){
            $font->size(60);
            $font->color('#000000');
            $font->align('center');
        });

        $canvas->text($year, 700, 400, function($font){
            $font->size(40);
            $font->color('#000000');
            $font->align('center');
        });

        $canvas->text("⭐ $rating", 700, 470, function($font){
            $font->size(40);
            $font->color('#f59e0b');
            $font->align('center');
        });

        //SUBTITULO
        $canvas->text("Deberías ver:", 600, 650, function($font){
            $font->size(60);
            $font->color('#1e3a8a');
            $font->align('center');
        });

        //GRID 2x3
        $xStart = 100;
        $yStart = 750;
        $x = $xStart;
        $y = $yStart;
        $col = 0;

        foreach($recs as $rec)
        {
            $movie = TMDBService::getDetails($rec->tmdb_id,$busqueda->tipo);
            if(!$movie || empty($movie['poster_path'])) continue;

            try {
                $res = Http::get("https://image.tmdb.org/t/p/w300".$movie['poster_path']);
                if ($res->successful()) {
                    $poster = $manager->read($res->body())->resize(220, 320);
                    $canvas->place($poster, 'top-left', $x, $y);
                }
            } catch (\Exception $e) {}

            $name = $movie['title'] ?? $movie['name'] ?? "Sin título";
            $year = substr($movie['release_date'] ?? $movie['first_air_date'] ?? "",0,4);
            $rating = number_format($movie['vote_average'] ?? 0,1);

            //TEXTO
            $canvas->text($name, $x + 110, $y + 340, function($font){
                $font->size(28);
                $font->color('#000000');
                $font->align('center');
            });

            $canvas->text($year, $x + 110, $y + 380, function($font){
                $font->size(24);
                $font->color('#000000');
                $font->align('center');
            });

            $canvas->text("⭐ $rating", $x + 110, $y + 420, function($font){
                $font->size(24);
                $font->color('#f59e0b');
                $font->align('center');
            });

            $x += 350;
            $col++;

            if ($col == 3) {
                $col = 0;
                $x = $xStart;
                $y += 500;
            }
        }

        //FOOTER BRANDING
        $canvas->text("HAMFUHUDDLE", 600, 1500, function($font){
            $font->size(40);
            $font->color('#1e3a8a');
            $font->align('center');
        });

        //NOMBRE DINÁMICO
        $filename = 'hamfuhuddle_' . now()->format('Y-m-d_H-i-s') . '.png';

        return response($canvas->toPng(), 200)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }
}