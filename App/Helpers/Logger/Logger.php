<?php

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