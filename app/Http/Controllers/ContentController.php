<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ContentController extends Controller
{
    public function show($id, $type)
    {
        $data = TMDBService::getDetailsFull($id, $type);

        return view('show', compact('data', 'type'));
    }

    public function marcarVisto(Request $request)
    {
        DB::table('vistas')->updateOrInsert(
            [
                'usuario_id' => Auth::id(),
                'tmdb_id' => $request->tmdb_id,
                'tipo' => $request->type
            ],
            [
                'fecha_vista' => now()
            ]
        );

        return response()->json(['ok'=>true]);
    }

    public function calificar(Request $request)
    {
        DB::table('calificaciones')->updateOrInsert(
            [
                'usuario_id' => Auth::id(),
                'tmdb_id' => $request->tmdb_id,
                'tipo' => $request->type
            ],
            [
                'calificacion' => $request->calificacion
            ]
        );

        return response()->json(['ok'=>true]);
    }
}