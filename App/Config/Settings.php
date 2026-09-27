<?php

namespace App\Config;

/**
 * La configuración de la aplicación, en un solo lugar.
 *
 * Empieza con los valores fijos de abajo y, al arrancar, el Bootstrap le agrega:
 *   - los valores del archivo .env (DB_DRIVER, HOST, APP_DEBUG, ...)
 *   - 'controller': el nombre de la página actual ("Index", "Home", ...)
 *
 * Se lee desde cualquier parte con Settings::get('CLAVE').
 */
class Settings {

    private static $settings =
    [
        // Hojas de estilo externas: la fuente Poppins y los iconos del formulario de login.
        'external-css' =>
        [
            'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap',
            'https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css',
        ],

        // CSS y JS que cargan todas las páginas.
        'master-js' => 'public/js-min/master.min.js',
        'master-css' => 'public/css-min/master.min.css',

        // CSS y JS de cada página: {cssView} / {jsView} se cambian por el nombre de la página,
        // por ejemplo "Home" -> public/css-min/Home.min.css
        'css-min-path' => 'public/css-min/{cssView}.min.css',
        'js-min-path'  => 'public/js-min/{jsView}.min.js',
    ];


    /**
     * Devuelve el valor guardado, o null si la clave no existe.
     */
    public static function get(string $key)
    {
        return self::$settings[$key] ?? null;
    }

    /**
     * Guarda un valor. Ojo: si la clave ya existe, el valor se agrega a una lista (array)
     * en lugar de reemplazar al anterior.
     */
    public static function set(string $key, string $val): void
    {
        if (array_key_exists($key, self::$settings)) {
            self::$settings[$key][] = $val;
        } else {
            self::$settings[$key] = $val;
        }
    }

}
