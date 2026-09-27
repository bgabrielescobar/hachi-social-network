<?php

/**
 * Guarda mensajes en memoria con la fecha y hora, para revisarlos después con getData().
 *
 * Nota: esta clase todavía no se usa en el proyecto. Como no tiene namespace,
 * el autoloader no la encuentra: para usarla habría que hacer
 * require 'App/Helpers/Logger/Logger.php' o agregarle "namespace App\Helpers\Logger;".
 */
class Logger {

    private static $data;

    public static function getData()
    {
        return self::$data;
    }

    public static function set($key, $log)
    {
        self::$data[$key] = date('Y-m-d H:i:s') . ' : ' . $log . "\n";
    }
}
