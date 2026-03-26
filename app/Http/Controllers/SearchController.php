<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TMDBService;
use App\Models\Busqueda;
use Illuminate\Support\Facades\Auth;
use App\Traits\ContentFilter;

class SearchController extends Controller
{
    use ContentFilter;

    public function search(Request $request)
    {
        $query = $request->query('query');

        if(!$query){
            return response()->json([]);
        }

        $results = TMDBService::search($query);
        
        $filteredResults = array_filter($results, [$this, 'isSafe']);

        return response()->json(array_values($filteredResults));
    }

    public function selectMovie($id, $type)
    {
        $busqueda = Busqueda::create([
            'usuario_id' => auth()->id(),
            'tmdb_id' => $id,
            'tipo' => $type
        ]);

        return redirect('/recommendations/'.$busqueda->id);
    }
}