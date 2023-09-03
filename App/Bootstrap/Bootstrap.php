<?php 

namespace App\Bootstrap;

use App\Config\Settings;
use App\Controller\IndexController;
use App\Helpers\Cookie\CookieManager;
use App\Helpers\Database\Facade;
use App\Helpers\Database\Singleton;
use App\Helpers\Database\User;

class Bootstrap {

    const PREFIX_NAMESPACE_CONTROLLER = "\App\Controller\\";

    public static function start(): void
    {
        self::initSet();
        self::classLoader();
        self::initEnv();
        self::initController();
        self::isUserLogged();
    }

    private static function isUserLogged()
    {
        $credential = CookieManager::getInstance()->getLoginCookie();

        if (empty($credential) && Settings::get('controller') == 'Index') {
            return;
        }

        if (empty($credential) && Settings::get('controller') != 'Index') {
            header('Location: index.php');
        }

        if (isset($credential) && Settings::get('controller') == 'Index') {
            header('Location: home.php');
        }

        Singleton::getFacade()->getUserClass()->selectLoginUser($credential['email'], $credential['pass']);

        // TODO: If user deleted account

    }

    private static function initSet()
    {
        ini_set("display_errors", 1);
    }

    private static function initEnv()
    {
        $file = dirname(__DIR__, 2) . '/.env';

        if (file_exists($file)) {
            $envContent = parse_ini_file($file);

            foreach ($envContent as $key => $content) {
                Settings::set($key, $content);
            }
        } else {
            echo "File not found.";
        }
    }

    private static function initController(): void
    {
        $scriptSelf = basename($_SERVER['PHP_SELF']);

        $preController =  ucfirst(str_replace(['/','.php'], '', $scriptSelf));

        Settings::set('controller', $preController);

        $controller = Bootstrap::PREFIX_NAMESPACE_CONTROLLER . $preController . 'Controller';

        unset($preController);

        $objController = new $controller;

        $objController->indexAction();
    }

    private static function classLoader (): void
    {
        spl_autoload_register(function($className) {
            $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
            $directories = [
                DIRECTORY_SEPARATOR,
                DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR,
                DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR . 'Base' . DIRECTORY_SEPARATOR,
                DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR,
                DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR,
                DIRECTORY_SEPARATOR . 'Module' . DIRECTORY_SEPARATOR,
            ];
            foreach ($directories as $directory) {
                $file = dirname(__DIR__, 2) . $directory . $className . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    break;
                }
            }
        });
    }

}