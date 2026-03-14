<?php

namespace App\Http\Controllers;

use App\Models\Busqueda;
use App\Models\Recomendacion;
use App\Services\TMDBService;

class RecommendationController extends Controller
{

    public function show($busquedaId)
    {

        $busqueda = Busqueda::findOrFail($busquedaId);

        $original = TMDBService::getDetails($busqueda->tmdb_id);

        $recs = Recomendacion::where('busqueda_id',$busquedaId)
                ->orderBy('posicion')
                ->get();

        $movies = [];

        foreach($recs as $rec)
        {
            $movies[] = TMDBService::getDetails($rec->tmdb_id);
        }

        return view('recommendations',[
            'original'=>$original,
            'movies'=>$movies
        ]);

    }

}