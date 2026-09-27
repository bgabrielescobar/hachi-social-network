<?php

namespace App\Bootstrap;

use App\Config\Settings;
use App\Helpers\Database\Singleton;
use App\Helpers\Session\SessionManager;

class Bootstrap {

    const PREFIX_NAMESPACE_CONTROLLER = "\App\Controller\\";

    /**
     * Pages that can be visited without being logged in.
     */
    const PUBLIC_CONTROLLERS = ['Index', 'Login', 'Register'];

    public static function start(): void
    {
        self::classLoader();
        self::initEnv();
        self::initSet();
        self::initController();
    }

    private static function checkAccess(string $controller): void
    {
        $session = SessionManager::getInstance();
        $userId = $session->getUserId();

        if ($userId !== null && !Singleton::getFacade()->getUserClass()->selectUserById($userId)) {
            // The account no longer exists, drop the stale session.
            $session->logout();
            $userId = null;
        }

        if ($userId === null && !in_array($controller, self::PUBLIC_CONTROLLERS)) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['code' => 1, 'title' => 'FAILED!', 'message' => 'Your session has expired, log in again']);
                exit;
            }
            self::redirect('index.php');
        }

        if ($userId !== null && $controller == 'Index') {
            self::redirect('home.php');
        }
    }

    private static function redirect(string $location): void
    {
        header('Location: ' . $location);
        exit;
    }

    private static function initSet()
    {
        ini_set("display_errors", Settings::get('APP_DEBUG') ? '1' : '0');
    }

    private static function initEnv()
    {
        $file = dirname(__DIR__, 2) . '/.env';

        if (!file_exists($file)) {
            http_response_code(500);
            die('Missing .env file in the project root.');
        }

        $envContent = parse_ini_file($file);

        foreach ($envContent as $key => $content) {
            Settings::set($key, $content);
        }
    }

    private static function initController(): void
    {
        $scriptSelf = basename($_SERVER['SCRIPT_NAME']);

        $preController =  ucfirst(str_replace(['/','.php'], '', $scriptSelf));

        Settings::set('controller', $preController);

        self::checkAccess($preController);

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
