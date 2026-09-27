<?php

namespace App\Helpers\Database;

use App\Helpers\Database\Facade;

/**
 * Da acceso a las tablas desde cualquier parte del código:
 *
 *     Singleton::getFacade()->getUserClass()->selectUserById(3);
 *
 * Patrón Singleton: siempre devuelve el mismo objeto Facade, así no se crean
 * objetos repetidos durante la petición.
 */
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
