<?php

namespace App\Config;

class Settings {

    private static $settings = 
    [
        'external-lib' =>
        [

        ],
        'master-js' => 'public/js-min/master.min.js',
        'master-css' => 'public/css-min/master.min.css',

        'css-min-path' => 'public/css-min/{cssView}.min.css',
        'js-min-path'  => 'public/js-min/{jsView}.min.js',
    ];
  

    public static function get(string $key)
    {
        return self::$settings[$key];
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