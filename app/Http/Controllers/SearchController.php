<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TMDBService;
use App\Models\Busqueda;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use App\Traits\ContentFilter;
use App\Jobs\UpdateDatasetJob;
use App\Models\Contenido;

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
        $userId = auth()->id();

        $data = TMDBService::getDetailsFull($id, $type);
        $genres = collect($data['genres'] ?? [])->pluck('name')->toArray();

        $cast = collect($data['credits']['cast'] ?? [])
            ->pluck('name')
            ->take(5)
            ->toArray();

        $director = collect($data['credits']['crew'] ?? [])
            ->firstWhere('job', 'Director')['name'] ?? null;

        $year = substr(
            $data['release_date'] ?? $data['first_air_date'] ?? '',
            0,
            4
        );

        Contenido::updateOrCreate(
            [
                'tmdb_id' => $id
            ],
            [
                'tipo' => $type,
                'titulo' => $data['title'] ?? $data['name'],
                'poster_path' => $data['poster_path'] ?? null,
                'generos' => json_encode($genres),
                'popularidad' => $data['popularity'] ?? 0,
                'overview' => $data['overview'] ?? '',
                'cast_list' => json_encode($cast),
                'director' => $director,
                'rating' => $data['vote_average'] ?? 0,
                'votes' => $data['vote_count'] ?? 0,
                'year' => $year,
                'language' => $data['original_language'] ?? 'en'
            ]
        );

        $busqueda = Busqueda::create([
            'usuario_id' => $userId,
            'tmdb_id' => $id,
            'tipo' => $type
        ]);

        $conteo = Contenido::count();

        $lastRun = DB::table('system_flags')
            ->where('key', 'last_training')
            ->value('value');

        if (!$lastRun) {
            DB::table('system_flags')->insert([
                'key' => 'last_training',
                'value' => time()
            ]);
            $lastRun = time();
        }

        if ($conteo % 50 == 0 && (time() - $lastRun > 3600)) {

            UpdateDatasetJob::dispatch();

            DB::table('system_flags')
                ->where('key', 'last_training')
                ->update(['value' => time()]);
        }

        return redirect('/recommendations/'.$busqueda->id);
    }
}