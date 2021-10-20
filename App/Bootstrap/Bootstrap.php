<?php 

namespace App\Bootstrap;

use App\Config\Settings;
use App\Controller\IndexController;
use App\Helpers\Database\Singleton;
use App\Helpers\Database\User;

class Bootstrap {

    public static function start(): void
    {

        self::initSet();
        self::classLoader();
        self::initDatabase();
        self::initController();

    }

    private static function initSet()
    {
        ini_set("display_errors", 1);
    }

    private static function initDatabase()
    {
        
    }

    private static function initController(): void
    {
        $scriptSelf = $_SERVER['PHP_SELF'];

        $preController =  ucfirst(str_replace(['/','.php'], '', $scriptSelf));
        Settings::putSetting('controller', $preController); 
        unset($preController);

        $controller = ucfirst(str_replace(['/','.php'], '', $scriptSelf)) . 'Controller';
        
        $objController = new $controller;

        $objController->indexAction();
    }

    private static function classLoader (): void
    { 
        spl_autoload_register( function($className) 
        {
            $directories = [   
                '\\',
                '\App\Controller\\',
                '\Helpers',
                '\Module',
            ];
            //To production include str_replace('\\', '/', $className) . '.php';
            foreach ($directories as $directory) {
                if (file_exists( dirname(__DIR__, 2)  . $directory . $className . '.php')) {
                    include dirname(__DIR__, 2) . $directory . $className . '.php';
                    break;
                }
            }
        });
    } 

}