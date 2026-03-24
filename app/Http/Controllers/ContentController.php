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

        $visto = DB::table('vistas')
            ->where('usuario_id', Auth::id())
            ->where('tmdb_id', $id)
            ->where('tipo', $type)
            ->exists();

        $calificacion = DB::table('calificaciones')
            ->where('usuario_id', Auth::id())
            ->where('tmdb_id', $id)
            ->where('tipo', $type)
            ->value('calificacion');

        $pageTitle = $data['display_title'];

        return view('show', compact('data', 'type', 'visto', 'calificacion', 'pageTitle')); 
    }

    public function marcarVisto(Request $request)
    {
        $userId = Auth::id();

        $existe = DB::table('vistas')
            ->where('usuario_id', $userId)
            ->where('tmdb_id', $request->tmdb_id)
            ->where('tipo', $request->type)
            ->exists();

        if ($existe) {
            DB::table('vistas')
                ->where('usuario_id', $userId)
                ->where('tmdb_id', $request->tmdb_id)
                ->where('tipo', $request->type)
                ->delete();

            return response()->json(['visto' => false]);
        } else {
            DB::table('vistas')->insert([
                'usuario_id' => $userId,
                'tmdb_id' => $request->tmdb_id,
                'tipo' => $request->type,
                'fecha_vista' => now()
            ]);

            return response()->json(['visto' => true]);
        }
    }

    public function calificar(Request $request)
    {
        $userId = Auth::id();

        $existente = DB::table('calificaciones')
            ->where('usuario_id', $userId)
            ->where('tmdb_id', $request->tmdb_id)
            ->where('tipo', $request->type)
            ->first();

        if ($existente && $existente->calificacion === $request->calificacion) {
            DB::table('calificaciones')
                ->where('usuario_id', $userId)
                ->where('tmdb_id', $request->tmdb_id)
                ->where('tipo', $request->type)
                ->delete();

            return response()->json(['calificacion' => null]);
        }

        DB::table('calificaciones')->updateOrInsert(
            [
                'usuario_id' => $userId,
                'tmdb_id' => $request->tmdb_id,
                'tipo' => $request->type
            ],
            [
                'calificacion' => $request->calificacion
            ]
        );

        return response()->json(['calificacion' => $request->calificacion]);
    }
}