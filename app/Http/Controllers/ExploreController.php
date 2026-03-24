<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TMDBService;

class ExploreController extends Controller
{
    public function index()
    {
        $movies = TMDBService::getPopular('movie');
        $tv = TMDBService::getPopular('tv');

        $pageTitle = 'Buscar';

        return view('explore', compact('movies', 'tv', 'pageTitle'));
    }

    public function search(Request $request)
    {
        return response()->json(
            TMDBService::search($request->q)
        );
    }

}