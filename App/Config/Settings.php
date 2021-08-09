<?php

namespace App\Config;

class Settings {

    private static $settings = 
    [
        'external-css' => 
        [
            'public/css-min/master.min.css'
        ],
        'css-min-path' => 'public/css-min/{cssView}.min.css',
        'js-min-path'  => 'public/js-min/{jsView}.min.js',

        // Standar view for all pages

        'bases-view' => 
        [
            'App\View\Head.view.php',

        ],

        'path-view' => 'App\View\{view-template}.view.php',

        /* To Do: change env variable */
        'db' => 
        [

        ]



    ];
  

    public static function getSetting(string $key)
    {
        return self::$settings[$key];
    }

    public static function putSetting(string $key, string $val): void
    {
        if (array_key_exists($key, self::$settings)) {
            self::$settings[$key][] = $val;
        } else {
            self::$settings[$key] = $val;
        }
    }

}