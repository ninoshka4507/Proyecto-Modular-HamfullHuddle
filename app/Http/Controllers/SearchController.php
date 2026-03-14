<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TMDBService;

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

    public function selectMovie($id)
    {
        //Redirige a la página de recomendaciones
        return redirect('/recommendations/'.$id);
    }

}