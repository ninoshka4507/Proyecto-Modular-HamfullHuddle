<?php

namespace App\Http\Controllers;

use App\Models\Busqueda;
use App\Models\Recomendacion;
use App\Services\TMDBService;
use Intervention\Image\ImageManagerStatic as Image;

class RecommendationController extends Controller
{

    public function show($busquedaId)
    {

        $busqueda = Busqueda::findOrFail($busquedaId);

        $userId = auth()->id();

        $tmdbId = $busqueda->tmdb_id;

        //ejecutar IA en python
        $script = base_path('ai/recommend.py');

        $command = "py \"$script\" $tmdbId $userId";

        $output = shell_exec($command);

        $lines = explode("\n", trim($output));

        //eliminar recomendaciones anteriores
        Recomendacion::where('busqueda_id',$busquedaId)->delete();

        $pos = 1;

        foreach($lines as $line)
        {

            if(!$line) continue;

            $parts = explode(" ",$line);

            if(count($parts) < 1) continue;

            $tmdb = $parts[0];

            Recomendacion::create([

                'busqueda_id'=>$busquedaId,
                'tmdb_id'=>$tmdb,
                'posicion'=>$pos

            ]);

            $pos++;

            if($pos > 6) break;

        }

        //obtener datos del contenido original desde TMDB
        $original = TMDBService::getDetails($busqueda->tmdb_id, $busqueda->tipo);

        $recs = Recomendacion::where('busqueda_id',$busquedaId)
                ->orderBy('posicion')
                ->get();

        $movies = [];

        foreach($recs as $rec)
        {
            $movies[] = TMDBService::getDetails($rec->tmdb_id, $busqueda->tipo);
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
            ->get();

        $canvas = Image::canvas(1200,850,'#0f172a');

        //titulo app
        $canvas->text("HAMFUHUDDLE",60,60,function($font){
            $font->size(42);
            $font->color('#a855f7');
        });

        $canvas->text("Fans of",60,120,function($font){
            $font->size(30);
            $font->color('#ffffff');
        });

        //poster original
        if(isset($original['poster_path']))
        {
            $poster = Image::make(
                "https://image.tmdb.org/t/p/w200".$original['poster_path']
            )->resize(120,180);

            $canvas->insert($poster,'top-left',60,150);
        }

        $title = $original['title'] ?? $original['name'] ?? "Unknown";

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

            if(isset($movie['poster_path']))
            {
                $poster = Image::make(
                    "https://image.tmdb.org/t/p/w300".$movie['poster_path']
                )->resize(200,300);

                $canvas->insert($poster,'top-left',$x,$y);
            }

            $name = $movie['title'] ?? $movie['name'] ?? "Sin título";

            $year = substr(
                $movie['release_date'] ??
                $movie['first_air_date'] ??
                "",
                0,
                4
            );

            $rating = $movie['vote_average'] ?? "N/A";

            $genres = "";

            if(isset($movie['genres']))
            {
                $g = array_slice($movie['genres'],0,2);
                $genres = implode(" ",array_column($g,"name"));
            }

            $canvas->text($name,$x,$y + 330,function($font){
                $font->size(20);
                $font->color('#ffffff');
            });

            $canvas->text("$year",$x,$y + 360,function($font){
                $font->size(18);
                $font->color('#cccccc');
            });

            $canvas->text("⭐ $rating 🎭 $genres",$x,$y + 385,function($font){
                $font->size(18);
                $font->color('#a855f7');
            });

            $x += 360;

            $count++;

            if($count == 3)
            {
                $x = 80;
                $y = 650;
            }

        }

        //footer
        $canvas->text("HamfuHuddle",1000,820,function($font){
            $font->size(26);
            $font->color('#a855f7');
        });

        return $canvas->response('png');

    }

}