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

        $badWords = [
            "bitch", "bitches", "sex", "porn", "xxx", "erotic", "nudity",
            "hentai", "bdsm", "escort", "orgy", "lust", "seduction", "playboy",
            "adult", "cum", "fart", "ass", "whore", "slut", "cock", "dick",
            "pussy", "boobs", "tits", "incest", "rape", "stepmom", "stepdad", "sensual"
        ];
        
        $badWordsPattern = '/\b(' . implode('|', $badWords) . ')\b/i';

        $filteredResults = array_filter($results, function($item) use ($badWordsPattern) {
            $itemData = (array) $item;
            
            if (isset($itemData['media_type'])) {
                if (!in_array($itemData['media_type'], ['movie', 'tv'])) {
                    return false; 
                }
            }

            if (isset($itemData['adult']) && $itemData['adult'] === true) {
                return false;
            }

            $title = $itemData['title'] ?? $itemData['name'] ?? $itemData['original_title'] ?? '';
            $overview = $itemData['overview'] ?? '';
            
            $fullText = $this->removeAccents(strtolower($title . ' ' . $overview));
            $cleanTitle = strtolower($title);

            if (preg_match($badWordsPattern, $fullText)) {
                return false;
            }

            if (strpos($cleanTitle, 'ghost story') !== false) {
                return false;
            }

            return true;
        });

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

    private function removeAccents($string) 
    {
        $unwanted_array = [
            'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y'
        ];
        return strtr($string, $unwanted_array);
    }
}