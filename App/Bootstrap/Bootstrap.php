<?php

namespace App\Bootstrap;

use App\Config\Settings;
use App\Helpers\Database\Singleton;
use App\Helpers\Session\SessionManager;

/**
 * Arranca la aplicación en cada petición.
 *
 * Todos los archivos de la raíz (index.php, home.php, ...) llaman a Bootstrap::start(),
 * que siempre hace los mismos pasos:
 *   1. Registra el autoloader, que carga las clases automáticamente cuando se usan.
 *   2. Lee la configuración del archivo .env.
 *   3. Decide si se muestran los errores de PHP (APP_DEBUG).
 *   4. Comprueba la sesión y ejecuta el controlador del archivo pedido.
 *
 * Más detalles en docs/2-arquitectura.md
 */
class Bootstrap {

    const PREFIX_NAMESPACE_CONTROLLER = "\App\Controller\\";

    /**
     * Páginas que se pueden visitar sin haber iniciado sesión.
     */
    const PUBLIC_CONTROLLERS = ['Index', 'Login', 'Register'];

    public static function start(): void
    {
        self::classLoader();
        self::initEnv();
        self::initSet();
        self::initController();
    }

    /**
     * Decide quién puede ver cada página:
     *   - Sin sesión solo se puede entrar a PUBLIC_CONTROLLERS. El resto redirige a index.php,
     *     o responde 401 si es una petición POST hecha desde JavaScript.
     *   - Con sesión, index.php (el login) redirige a home.php.
     */
    private static function checkAccess(string $controller): void
    {
        $session = SessionManager::getInstance();
        $userId = $session->getUserId();

        if ($userId !== null && !Singleton::getFacade()->getUserClass()->selectUserById($userId)) {
            // La cuenta ya no existe (por ejemplo, se borró la base de datos): cerramos esa sesión.
            $session->logout();
            $userId = null;
        }

        if ($userId === null && !in_array($controller, self::PUBLIC_CONTROLLERS)) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // A fetch() no le sirve una redirección: le respondemos un error JSON
                // y el JavaScript (postJson en master.min.js) manda al usuario al login.
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

    /**
     * Manda al navegador a otra página y termina la petición.
     */
    private static function redirect(string $location): void
    {
        header('Location: ' . $location);
        exit;
    }

    /**
     * Con APP_DEBUG=1 los errores de PHP se ven en la página: útil al programar,
     * pero en producción pueden mostrar datos internos, por eso va en 0.
     */
    private static function initSet()
    {
        ini_set("display_errors", Settings::get('APP_DEBUG') ? '1' : '0');
    }

    /**
     * Lee el archivo .env (líneas "CLAVE=valor") y guarda cada valor en Settings,
     * por ejemplo Settings::get('DB_DRIVER').
     */
    private static function initEnv()
    {
        $file = dirname(__DIR__, 2) . '/.env';

        if (!file_exists($file)) {
            http_response_code(500);
            die('Missing .env file, create it from .env.example');
        }

        $envContent = parse_ini_file($file);

        foreach ($envContent as $key => $content) {
            Settings::set($key, $content);
        }
    }

    /**
     * Convierte el archivo pedido en el nombre del controlador y lo ejecuta:
     * "/home.php" -> "Home" -> new \App\Controller\HomeController() -> indexAction()
     */
    private static function initController(): void
    {
        $scriptSelf = basename($_SERVER['SCRIPT_NAME']);

        $preController =  ucfirst(str_replace(['/','.php'], '', $scriptSelf));

        // Los módulos lo usan para elegir la vista, el CSS y el JS de la página.
        Settings::set('controller', $preController);

        self::checkAccess($preController);

        $controller = Bootstrap::PREFIX_NAMESPACE_CONTROLLER . $preController . 'Controller';

        unset($preController);

        $objController = new $controller;

        $objController->indexAction();
    }

    /**
     * Autoloader: cuando el código usa una clase que todavía no se cargó, PHP llama a esta
     * función con el nombre completo de la clase y aquí se hace el require de su archivo.
     *
     * El namespace coincide con las carpetas, por ejemplo:
     * App\Helpers\Session\SessionManager -> App/Helpers/Session/SessionManager.php
     * Los otros directorios de la lista son búsquedas extra para clases que no siguen esa regla.
     */
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
