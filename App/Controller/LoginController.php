<?php

namespace App\Controller;

use App\Controller\Base\Controller;
use App\Helpers\Database\Singleton;
use App\Helpers\Session\SessionManager;

/**
 * login.php: endpoint JSON que inicia sesión.
 *
 * Recibe {email, pass, remember-me} desde public/js-min/Index.min.js y responde
 * {code: 0} si el email y la contraseña son correctos. El JavaScript entonces va a home.php.
 */
class LoginController extends Controller {

    const FAILED_MESSAGE = "Wrong email or password";

    public function indexAction()
    {
        $data = $this->readJson();

        // Los emails se guardan en minúsculas: Ana@Mail.com y ana@mail.com son la misma cuenta.
        $email = strtolower(trim($data['email'] ?? ''));
        $pass = (string) ($data['pass'] ?? '');

        $userId = Singleton::getFacade()->getUserClass()->selectLoginUser($email, $pass);

        // El mismo mensaje si falla el email o la contraseña, así no se revela qué emails existen.
        if (!$userId) {
            $this->jsonError(self::FAILED_MESSAGE);
        }

        // "remember-me" solo llega cuando la casilla "Remember me" está marcada.
        SessionManager::getInstance()->login($userId, !empty($data['remember-me']));

        $this->jsonResponse(['code' => 0]);
    }

}
