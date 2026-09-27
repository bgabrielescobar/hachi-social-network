<?php

namespace App\Config;

class Settings {

    private static $settings =
    [
        'external-css' =>
        [
            'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap',
            'https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css',
        ],
        'master-js' => 'public/js-min/master.min.js',
        'master-css' => 'public/css-min/master.min.css',

        'css-min-path' => 'public/css-min/{cssView}.min.css',
        'js-min-path'  => 'public/js-min/{jsView}.min.js',
    ];


    public static function get(string $key)
    {
        return self::$settings[$key] ?? null;
    }

    public static function set(string $key, string $val): void
    {
        if (array_key_exists($key, self::$settings)) {
            self::$settings[$key][] = $val;
        } else {
            self::$settings[$key] = $val;
        }
    }

}
