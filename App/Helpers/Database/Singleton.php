<?php

namespace App\Helpers\Database;

use App\Helpers\Database\Facade;

class Singleton {

    private static $facadeInstance;

    public static function getFacade()
    {
        if (!self::$facadeInstance instanceof Facade) {
            self::$facadeInstance = new Facade();
        }

        return self::$facadeInstance;
    }

}