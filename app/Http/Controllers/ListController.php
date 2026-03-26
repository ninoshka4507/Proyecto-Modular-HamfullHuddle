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
            ->get();

        $listas = [
            'me_gusto' => [],
            'neutro' => [],
            'no_me_gusto' => [],
            'vistas' => []
        ];

        $peliculasCacheadas = [];

        foreach ($vistas as $item) {
            $key = $item->tmdb_id . '_' . $item->tipo;
            
            $data = TMDBService::getDetails($item->tmdb_id, $item->tipo);

            $contenido = [
                'id' => $item->tmdb_id,
                'type' => $item->tipo,
                'title' => $data['display_title'] ?? 'Unknown',
                'poster' => $data['poster_path'] ?? null,
                'year' => $data['release_date'] ?? $data['first_air_date'] ?? null
            ];

            $peliculasCacheadas[$key] = $contenido;
            $listas['vistas'][] = $contenido;
        }

        foreach ($calificaciones as $cal) {
            $key = $cal->tmdb_id . '_' . $cal->tipo;

            if (isset($peliculasCacheadas[$key])) {
                $contenido = $peliculasCacheadas[$key];
            } else {
                $data = TMDBService::getDetails($cal->tmdb_id, $cal->tipo);
                $contenido = [
                    'id' => $cal->tmdb_id,
                    'type' => $cal->tipo,
                    'title' => $data['display_title'] ?? 'Unknown',
                    'poster' => $data['poster_path'] ?? null,
                    'year' => $data['release_date'] ?? $data['first_air_date'] ?? null
                ];
                $peliculasCacheadas[$key] = $contenido;
            }

            if ($cal->calificacion === 'me_gusto') {
                $listas['me_gusto'][] = $contenido;
            } elseif ($cal->calificacion === 'neutro') {
                $listas['neutro'][] = $contenido;
            } elseif ($cal->calificacion === 'no_me_gusto') {
                $listas['no_me_gusto'][] = $contenido;
            }
        }

        $pageTitle = 'Mis listas';

        return view('listas', compact('listas', 'pageTitle'));
    }
}