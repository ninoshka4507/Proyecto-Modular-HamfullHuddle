<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\TMDBService;

class ListController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $vistas = DB::table('vistas')
            ->where('usuario_id', $userId)
            ->get();

        $calificaciones = DB::table('calificaciones')
            ->where('usuario_id', $userId)
            ->get()
            ->keyBy(fn($c) => $c->tmdb_id.'_'.$c->tipo);

        $listas = [
            'me_gusto' => [],
            'neutro' => [],
            'no_me_gusto' => [],
            'vistas' => []
        ];

        foreach ($vistas as $item) {

            $key = $item->tmdb_id . '_' . $item->tipo;
            $calificacion = $calificaciones[$key]->calificacion ?? null;

            $data = \App\Services\TMDBService::getDetails($item->tmdb_id, $item->tipo);

            $contenido = [
                'id' => $item->tmdb_id,
                'type' => $item->tipo,
                'title' => $data['display_title'] ?? 'Unknown',
                'poster' => $data['poster_path'] ?? null,
                'year' => $data['release_date'] ?? $data['first_air_date'] ?? null
            ];

            $listas['vistas'][] = $contenido;

            if ($calificacion === 'me_gusto') {
                $listas['me_gusto'][] = $contenido;
            } elseif ($calificacion === 'neutro') {
                $listas['neutro'][] = $contenido;
            } elseif ($calificacion === 'no_me_gusto') {
                $listas['no_me_gusto'][] = $contenido;
            }
        }

        $pageTitle = 'Mis listas';

        return view('listas', compact('listas', 'pageTitle'));
    }
}