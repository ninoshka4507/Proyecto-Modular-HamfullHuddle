<?php

namespace App\Traits;

trait ContentFilter
{
    protected function isSafe($item) 
    {
        $itemData = (array) $item;

        if (isset($itemData['media_type']) && $itemData['media_type'] === 'person') {
            return false;
        }
        
        if (($itemData['adult'] ?? false) === true) return false;
        
        if (($itemData['vote_average'] ?? 0) < 5) return false;
      
        if (isset($itemData['vote_count']) && $itemData['vote_count'] < 10) return false;

        $allowedLanguages = ['en', 'es', 'ja', 'ko', 'zh', 'fr', 'it', 'de', 'pt'];
        if (isset($itemData['original_language']) && !in_array($itemData['original_language'], $allowedLanguages)) {
            return false;
        }

        $title = $itemData['title'] ?? $itemData['name'] ?? $itemData['original_title'] ?? '';
        $overview = $itemData['overview'] ?? '';
        
        $fullText = $this->removeAccents(strtolower($title . ' ' . $overview));

        $badWords = [
            "porn", "xxx", "erotica", "hentai", "bdsm", "orgy", "kamasutra", 
            "nympho", "hot girls wanted", "erotic", "stepmom", "stepdad", "sensuelle"
        ];
        
        $badWordsPattern = '/\b(' . implode('|', $badWords) . ')\b/i';

        if (preg_match($badWordsPattern, $fullText)) {
            return false;
        }

        return true;
    }

    protected function removeAccents($string) 
    {
        $unwanted_array = [
            'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y'
        ];
        return strtr($string, $unwanted_array);
    }
}