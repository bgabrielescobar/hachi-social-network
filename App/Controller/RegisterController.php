<?php

namespace App\Controller;

use App\Controller\Base\Controller;
use App\Helpers\Database\Singleton;
use App\Helpers\Session\SessionManager;

/**
 * register.php: endpoint JSON que crea una cuenta.
 *
 * Recibe {first_name, last_name, email, pass, re_pass} desde public/js-min/Index.min.js.
 * Si los datos son válidos crea el usuario, inicia su sesión y responde {code: 0}.
 */
class RegisterController extends Controller {

    const MIN_PASSWORD_LENGTH = 6;

    /**
     * Igual al tamaño de las columnas first_name y last_name (varchar(50)).
     */
    const MAX_NAME_LENGTH = 50;

    public function indexAction()
    {
        $data = $this->readJson();

        // trim() quita los espacios del principio y del final.
        $userData = [
            'first_name' => trim($data['first_name'] ?? ''),
            'last_name' => trim($data['last_name'] ?? ''),
            'email' => strtolower(trim($data['email'] ?? '')),
            'pass' => (string) ($data['pass'] ?? ''),
        ];

        $error = $this->validate($userData, (string) ($data['re_pass'] ?? ''));

        if ($error) {
            $this->jsonError($error);
        }

        $userId = Singleton::getFacade()->getUserClass()->insertUser($userData);

        // La cuenta nueva entra directamente, sin tener que hacer login.
        SessionManager::getInstance()->login($userId, false);

        $this->jsonResponse(['code' => 0]);
    }

    /**
     * Devuelve el mensaje de error para el usuario, o null si los datos son válidos.
     * El formulario también valida en el navegador, pero eso se puede saltar:
     * la validación que cuenta es la del servidor.
     */
    private function validate(array $userData, string $repeatedPass): ?string
    {
        if ($userData['first_name'] === '' || $userData['last_name'] === '') {
            return "Enter your first and last name";
        }

        if (mb_strlen($userData['first_name']) > self::MAX_NAME_LENGTH || mb_strlen($userData['last_name']) > self::MAX_NAME_LENGTH) {
            return "Names can have up to " . self::MAX_NAME_LENGTH . " characters";
        }

        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            return "Enter a valid email";
        }

        if (strlen($userData['pass']) < self::MIN_PASSWORD_LENGTH) {
            return "The password needs at least " . self::MIN_PASSWORD_LENGTH . " characters";
        }

        if ($userData['pass'] !== $repeatedPass) {
            return "Passwords do not match";
        }

        if (Singleton::getFacade()->getUserClass()->selectEmailUser($userData['email'])) {
            return "That email is already registered, log in instead";
        }

        return null;
    }

}
