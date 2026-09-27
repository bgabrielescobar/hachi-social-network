<?php

namespace App\Controller\Base;

use App\Config\Settings;

abstract class Controller {

    const MODEL = "Module";

    const PREFIX_MODULE = 'App\Module\\';

    const FAILED_TITLE = "FAILED!";

    protected $data = null;

    protected function postController(): void
    {
        $moduleObject =  Controller::PREFIX_MODULE . Settings::get('controller') . self::MODEL;
        (new $moduleObject())->indexModel($this->data);
    }

    /**
     * Reads the JSON body sent by the front end. Requiring the JSON content type
     * also stops other sites from posting forms to our endpoints.
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

    protected function jsonResponse(array $response, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    protected function jsonError(string $message, int $status = 200): void
    {
        $this->jsonResponse(['code' => 1, 'title' => self::FAILED_TITLE, 'message' => $message], $status);
    }

}
