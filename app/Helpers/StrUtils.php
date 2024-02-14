<?php

namespace App\Helpers;

class StrUtils {
    public static function stripAccents($string)
    {
        $transliterator = \Transliterator::createFromRules(':: NFD; :: [:Mn:] Remove; :: NFC;');
        return  $transliterator->transliterate($string);
    }

}