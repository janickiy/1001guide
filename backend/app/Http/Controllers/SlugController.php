<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SlugController extends Controller
{

    /**
     * Make slug from name
     *
     * @param string $name
     * @param null|string $locale
     *
     * @return string
     */
    public static function slugFromName ($name, $locale=null) {
        // transliterate
        if ( $locale )
            setlocale(LC_ALL, $locale);
        $translit = iconv('UTF-8', 'ASCII//TRANSLIT', $name);

        // remove unwanted characters
        $stripped = preg_replace("/[^a-zA-Z0-9\s]/", "", $translit);

        // replace spaces to dashes
        $dashed = str_replace(' ', '-', $stripped);

        return strtolower($dashed);
    }


}
