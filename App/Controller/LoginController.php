<?php

namespace App\Controller;

use App\Helpers\Database\Singleton;
use App\Helpers\Cookie\CookieManager;

class LoginController {

    public function indexAction()
    {
        $data = json_decode(file_get_contents('php://input'), 1);

        $loginResult = Singleton::getFacade()->getUserClass()->selectLoginUser($data['email'], $data['pass']);

        if ($loginResult) {

            CookieManager::getInstance()->setLoginCookie($data);
            echo json_encode(['code' => 0]);

        } else {

            echo json_encode(['code' => 1]);

        }
        die;
    }

}