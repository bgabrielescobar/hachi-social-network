<?php

namespace App\Controller;

use App\Controller\Base\Controller;
use App\Helpers\Database\Singleton;
use App\Helpers\Session\SessionManager;

class LoginController extends Controller {

    const FAILED_MESSAGE = "Wrong email or password";

    public function indexAction()
    {
        $data = $this->readJson();

        $email = strtolower(trim($data['email'] ?? ''));
        $pass = (string) ($data['pass'] ?? '');

        $userId = Singleton::getFacade()->getUserClass()->selectLoginUser($email, $pass);

        if (!$userId) {
            $this->jsonError(self::FAILED_MESSAGE);
        }

        SessionManager::getInstance()->login($userId, !empty($data['remember-me']));

        $this->jsonResponse(['code' => 0]);
    }

}
