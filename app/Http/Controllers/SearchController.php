<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TMDBService;
use App\Models\Busqueda;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{

    public function search(Request $request)
    {
        $query = $request->query('query');

        if(!$query){
            return response()->json([]);
        }

        $results = TMDBService::search($query);

        return response()->json($results);
    }

    public function selectMovie($id,$type)
    {

        $busqueda = Busqueda::create([

            'usuario_id'=>auth()->id(),
            'tmdb_id'=>$id,
            'tipo'=>$type

        ]);

        return redirect('/recommendations/'.$busqueda->id);

    }

}