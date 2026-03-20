<?php

namespace App\Http\Controllers;

use App\Models\Busqueda;
use App\Models\Recomendacion;
use App\Services\TMDBService;
use Illuminate\Support\Facades\Http;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class RecommendationController extends Controller
{
    public function show($busquedaId)
    {
        $busqueda = Busqueda::findOrFail($busquedaId);
        $userId = auth()->id() ?? 0;
        $tmdbId = $busqueda->tmdb_id;

        //ejecutar IA
        $script = base_path('ai/recommend.py');
        $command = "py \"$script\" $tmdbId $userId";
        $output = shell_exec($command);

        $lines = [];
        
        if ($output && trim($output) !== "fallback") {
            $lines = explode("\n", trim($output));
        }

        Recomendacion::where('busqueda_id',$busquedaId)->delete();
        $pos = 1;

        foreach($lines as $line)
        {
            if(!$line) continue;
            $parts = explode("|",$line);
            if(count($parts) < 4) continue;

            $tmdb = (int) $parts[0];

            Recomendacion::create([
                'busqueda_id'=>$busquedaId,
                'tmdb_id'=>$tmdb,
                'posicion'=>$pos
            ]);

            $pos++;
            if($pos > 100) break;
        }

        $original = TMDBService::getDetails($busqueda->tmdb_id, $busqueda->tipo);
        $movies = $this->getFinalMovies($busqueda);

        return view('recommendations',[
            'original'=>$original,
            'movies'=>$movies,
            'busquedaId'=>$busquedaId
        ]);
    }

    private function getFinalMovies($busqueda)
    {
        $recs = Recomendacion::where('busqueda_id', $busqueda->id)
            ->orderBy('posicion')
            ->get();

        $movies = [];

        foreach($recs as $rec)
        {
            if(count($movies) >= 6) break;

            $movie = TMDBService::getDetails($rec->tmdb_id, $busqueda->tipo);

            if(!$movie) continue;
            if(empty($movie['poster_path'])) continue;
            if(($movie['vote_average'] ?? 0) < 5) continue;
            if(($movie['vote_count'] ?? 0) < 150) continue;

            if(!collect($movies)->contains('id', $movie['id'])){
                $movies[] = $movie;
            }
        }

        //FALLBACK 1
        if(count($movies) < 6)
        {
            $similar = TMDBService::getSimilar($busqueda->tmdb_id, $busqueda->tipo);

            foreach($similar as $movie)
            {
                if(count($movies) >= 6) break;

                if(empty($movie['poster_path'])) continue;
                if(($movie['adult'] ?? false) === true) continue;
                if(($movie['vote_count'] ?? 0) < 150) continue;
                if(($movie['vote_average'] ?? 0) < 5) continue;

                if(!collect($movies)->contains('id', $movie['id'])){
                    $movies[] = $movie;
                }
            }
        }

        //FALLBACK 2
        if(count($movies) < 6)
        {
            $original = TMDBService::getDetails($busqueda->tmdb_id, $busqueda->tipo);
            $primaryGenre = $original['genres'][0]['id'] ?? null;

            if ($primaryGenre) {
                $popular = TMDBService::getPopularByGenre($busqueda->tipo, $primaryGenre);

                foreach($popular as $movie)
                {
                    if(count($movies) >= 6) break;

                    if(empty($movie['poster_path'])) continue;
                    if(($movie['adult'] ?? false) === true) continue;
                    if(($movie['vote_count'] ?? 0) < 150) continue;
                    if(($movie['vote_average'] ?? 0) < 5) continue;

                    if(!collect($movies)->contains('id', $movie['id'])){
                        $movies[] = $movie;
                    }
                }
            }
        }

        return $movies;
    }
    
public function shareCard($busquedaId)
    {
        //Aumentamos el límite de memoria temporalmente a 512MB
        ini_set('memory_limit', '512M'); 
        
        //Darle más tiempo máximo de ejecución
        ini_set('max_execution_time', '120');

        $busqueda = Busqueda::findOrFail($busquedaId);

        $movies = $this->getFinalMovies($busqueda);
        $original = TMDBService::getDetails($busqueda->tmdb_id, $busqueda->tipo);

        //DESCARGA CONCURRENTE DE POSTERS (Súper Rápido)
        $posterUrls = [];
        
        if (!empty($original['poster_path'])) {
            $posterUrls['main'] = "https://image.tmdb.org/t/p/w500" . $original['poster_path'];
        }

        foreach ($movies as $index => $movie) {
            if (!empty($movie['poster_path'])) {
                $posterUrls['rec_' . $index] = "https://image.tmdb.org/t/p/w300" . $movie['poster_path'];
            }
        }

        //TIMEOUTS: Si TMDB tarda más de 3 segundos en un póster, se cancela para no trabar
        $responses = Http::pool(function (\Illuminate\Http\Client\Pool $pool) use ($posterUrls) {
            $requests = [];
            foreach ($posterUrls as $key => $url) {
                $requests[] = $pool->as($key)->timeout(3)->retry(2, 100)->get($url);
            }
            return $requests;
        });

        $manager = new ImageManager(new Driver());
        
        //Cargamos fondo directamente desde public/images
        $bgPath = public_path('images/bg-gradient.jpg');
        
        if (file_exists($bgPath)) {
            $canvas = $manager->read($bgPath);
        } else {
            $canvas = $manager->create(1080, 1920);
            $canvas->fill('#2e1065'); 
        }

        $lilac = '#B094FF';   
        $white = '#FFFFFF';   
        $green = '#00E5FF';   
        $purple = '#432B87';

        $fontBold = public_path('fonts/Montserrat-Black.ttf');
        $fontMedium = public_path('fonts/Montserrat-Medium.ttf');

        //Función para la estrellita
        $drawStar = function($x, $y, $size) use ($canvas, $manager) {
            $path = public_path('emojis/star.png');
            if (file_exists($path)) {
                $img = $manager->read($path)->resize($size, $size);
                $canvas->place($img, 'top-left', $x, $y);
            }
        };

        //Función de Auto Scale de texto
        $wrapText = function($text, $maxWidth, $fontPath, $fontSize, $maxHeight = null) {
            $originalSize = $fontSize;

            do {
                $words = explode(' ', $text);
                $lines = [];
                $current = '';

                foreach ($words as $word) {
                    $test = $current ? $current . ' ' . $word : $word;
                    $box = imagettfbbox($fontSize, 0, $fontPath, $test);
                    $width = $box[2] - $box[0];

                    if ($width <= $maxWidth) {
                        $current = $test;
                    } else {
                        if ($current) $lines[] = $current;
                        $current = $word;
                    }
                }

                if ($current) $lines[] = $current;

                $lineHeight = $fontSize + 6;
                $totalHeight = count($lines) * $lineHeight;

                if (!$maxHeight || $totalHeight <= $maxHeight) {
                    return [
                        'text' => implode("\n", $lines),
                        'fontSize' => $fontSize,
                        'lineHeight' => $lineHeight
                    ];
                }

                $fontSize -= 2;

            } while ($fontSize > ($originalSize * 0.7));

            return [
                'text' => implode("\n", $lines),
                'fontSize' => $fontSize,
                'lineHeight' => $lineHeight
            ];
        };

        //===== HEADER =====
        $canvas->text("HAMFUHUDDLE", 80, 70, function($font) use ($fontBold, $lilac){
            $font->file($fontBold);
            $font->size(85);
            $font->color($lilac);
            $font->valign('top'); 
        });

        $canvas->text("Porque viste", 80, 160, function($font) use ($fontMedium, $lilac){
            $font->file($fontMedium);
            $font->size(45);
            $font->color($lilac);
            $font->valign('top'); 
        });

        //===== POSTER PRINCIPAL =====
        $posterX = 80;
        $posterY = 230;
        $posterW = 260;
        $posterH = 390;
        $padding = 20;

        if (isset($responses['main']) && $responses['main']->successful()) {
            $poster = $manager->read($responses['main']->body())->resize($posterW, $posterH);
            $canvas->place($poster, 'top-left', $posterX, $posterY);
        }

        $centerY = $posterY + ($posterH / 2);
        $contentStartX = $posterX + $posterW + $padding;
        $contentWidth = (1080 - 80) - $contentStartX;

        $titleData = $wrapText(
            $original['title'] ?? $original['name'] ?? "Unknown",
            $contentWidth,
            $fontBold,
            46,
            220
        );

        $title = $titleData['text'];
        $fontSize = $titleData['fontSize'];
        $lineHeight = $titleData['lineHeight'];

        $linesCount = substr_count($title, "\n") + 1;
        $titleHeight = $linesCount * $lineHeight;

        $year = substr($original['release_date'] ?? $original['first_air_date'] ?? "",0,4);
        $rating = number_format($original['vote_average'] ?? 0,1);

        $totalHeight = $titleHeight + 15 + 38 + 20 + 38;
        $startY = $centerY - ($totalHeight / 2);

        $canvas->text($title, $contentStartX, $startY, function($font) use ($fontBold, $white, $fontSize){
            $font->file($fontBold);
            $font->size($fontSize);
            $font->color($white);
            $font->valign('top'); 
        });

        $yearY = $startY + $titleHeight + 15;
        $canvas->text($year, $contentStartX, $yearY, function($font) use ($fontMedium, $lilac){
            $font->file($fontMedium);
            $font->size(38);
            $font->color($lilac);
            $font->valign('top'); 
        });

        $ratingY = $yearY + 38 + 15; 
        $starSize = 28;
        $fontSizeMain = 38;
        $centroYMain = $ratingY + ($fontSizeMain / 2); 

        $drawStar($contentStartX, $centroYMain - ($starSize / 2) - 2, $starSize); 

        $canvas->text($rating, $contentStartX + $starSize + 12, $centroYMain, function($font) use ($fontMedium, $white, $fontSizeMain){
            $font->file($fontMedium);
            $font->size($fontSizeMain);
            $font->color($white);
            $font->valign('middle'); 
        });

        //===== SECTION =====
        $canvas->text("Deberías ver:", 80, 650, function($font) use ($fontBold, $green){
            $font->file($fontBold);
            $font->size(55);
            $font->color($green);
            $font->valign('top'); 
        });

        //===== GRID =====
        $x = 80;
        $y = 730;
        $col = 0;

        foreach($movies as $index => $movie)
        {
            $cardW = 440;
            $cardH = 350; 
            $padding = 20;
            $posterW = 190; 
            $posterH = 285; 

            $canvas->drawRectangle($x, $y, function ($rect) use ($purple, $cardW, $cardH) {
                $rect->size($cardW, $cardH);
                $rect->background($purple);
            });

            $recKey = 'rec_' . $index;
            if (isset($responses[$recKey]) && $responses[$recKey]->successful()) {
                $poster = $manager->read($responses[$recKey]->body())->resize($posterW, $posterH);
                $canvas->place($poster, 'top-left', $x + $padding, $y + ($cardH - $posterH)/2);
            }

            $contentX = $x + $padding + $posterW + 20;
            $contentWidth = $cardW - ($posterW + 3*$padding);

            $titleData = $wrapText(
                $movie['title'] ?? $movie['name'] ?? "Sin título",
                $contentWidth,
                $fontBold,
                28, 
                160 
            );

            $name = $titleData['text'];
            $fontSize = $titleData['fontSize'];
            $lineHeight = $titleData['lineHeight'];

            $linesCount = substr_count($name, "\n") + 1;
            $titleHeight = $linesCount * $lineHeight;

            $year = substr($movie['release_date'] ?? $movie['first_air_date'] ?? "",0,4);
            $rating = number_format($movie['vote_average'] ?? 0,1);

            $totalHeight = $titleHeight + 15 + 26 + 20 + 26;
            $startY = $y + ($cardH - $totalHeight) / 2;

            $canvas->text($name, $contentX, $startY, function($font) use ($fontBold, $white, $fontSize){
                $font->file($fontBold);
                $font->size($fontSize);
                $font->color($white);
                $font->valign('top'); 
            });

            $yearY = $startY + $titleHeight + 15;
            $canvas->text($year, $contentX, $yearY, function($font) use ($fontMedium, $lilac){
                $font->file($fontMedium);
                $font->size(26); 
                $font->color($lilac);
                $font->valign('top'); 
            });

            $ratingY = $yearY + 26 + 15;
            $starSizeCard = 22;
            $fontSizeCard = 26;
            $centroYCard = $ratingY + ($fontSizeCard / 2); 

            $drawStar($contentX, $centroYCard - ($starSizeCard / 2) - 2, $starSizeCard); 

            $canvas->text($rating, $contentX + $starSizeCard + 8, $centroYCard, function($font) use ($fontMedium, $white, $fontSizeCard){
                $font->file($fontMedium);
                $font->size($fontSizeCard); 
                $font->color($white);
                $font->valign('middle'); 
            });

            $x += 480;
            $col++;

            if ($col == 2) {
                $col = 0;
                $x = 80;
                $y += 370; 
            }
        }

        //===== FOOTER ===== 
        $marginRight = 15;
        $marginBottom = 15;

        $logoPath = public_path('images/logo.png'); 
        $logoSize = 70; 
        $spacing = 15; 
        $text = "HamfuHuddle"; 

        $box = imagettfbbox(34, 0, $fontBold, $text);
        $textWidth = $box[2] - $box[0]; 

        $totalWidth = $logoSize + $spacing + $textWidth; 
        
        $startX = 1080 - $marginRight - $totalWidth; 
        $logoY = 1920 - $marginBottom - $logoSize; 
        $footerY = $logoY + ($logoSize / 2); 

        if (file_exists($logoPath)) { 
            $logo = $manager->read($logoPath)->resize($logoSize, $logoSize); 
            $canvas->place($logo, 'top-left', $startX, $logoY); 
        } 
            
        $canvas->text($text, $startX + $logoSize + $spacing, $footerY, function($font) use ($fontBold, $lilac) { 
            $font->file($fontBold); 
            $font->size(34); 
            $font->color($lilac); 
            $font->valign('middle'); 
        });

        $filename = 'hamfuhuddle_' . now('America/Mexico_City')->format('d-m-Y_H-i') . '.png';

        return response($canvas->toPng(), 200)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }
}