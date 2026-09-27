<?php

namespace App\Controller\Base;

use App\Config\Settings;

/**
 * Clase base de todos los controladores (App/Controller).
 *
 * El controlador recibe la petición, pide los datos a la base de datos y decide la respuesta.
 * Hay dos tipos:
 *   - Páginas (index.php, home.php): guardan los datos en $this->data y llaman a
 *     postController() para mostrar el HTML.
 *   - Endpoints JSON (login.php, register.php, post.php): leen la petición con readJson()
 *     y contestan con jsonResponse() o jsonError().
 *
 * Todas las respuestas JSON llevan 'code': 0 si salió bien y 1 si hubo un error.
 */
abstract class Controller {

    const MODEL = "Module";

    const PREFIX_MODULE = 'App\Module\\';

    const FAILED_TITLE = "FAILED!";

    /**
     * Datos que el controlador le pasa al módulo y a la vista.
     */
    protected $data = null;

    /**
     * Pasa $this->data al módulo que tiene el mismo nombre que el controlador
     * (HomeController -> HomeModule), y el módulo muestra la página.
     */
    protected function postController(): void
    {
        $moduleObject =  Controller::PREFIX_MODULE . Settings::get('controller') . self::MODEL;
        (new $moduleObject())->indexModel($this->data);
    }

    /**
     * Lee el cuerpo JSON que envía el JavaScript y lo devuelve como array.
     * Exigir el tipo "application/json" también impide que otras webs envíen
     * formularios a nuestros endpoints (ver CSRF en docs/5-seguridad.md).
     */
    protected function readJson(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || stripos($contentType, 'application/json') !== 0) {
            $this->jsonError('Invalid request', 400);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        return is_array($data) ? $data : [];
    }

    /**
     * Envía la respuesta en formato JSON y termina la petición: el código que viene
     * después de llamar a este método ya no se ejecuta.
     */
    protected function jsonResponse(array $response, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    /**
     * Respuesta de error: {code: 1, title, message}.
     * El JavaScript muestra title y message en la alerta roja.
     */
    protected function jsonError(string $message, int $status = 200): void
    {
        $this->jsonResponse(['code' => 1, 'title' => self::FAILED_TITLE, 'message' => $message], $status);
    }

}
