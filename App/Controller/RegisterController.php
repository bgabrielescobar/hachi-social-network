<?php

namespace App\Controller;

use App\Helpers\Database\Singleton;

class RegisterController {
    

    public function indexAction()
    {
        $registerStatus = false;

        $data = json_decode(file_get_contents('php://input'), 1);

        $isNewEmail = Singleton::getFacade()->getUserInstance()->selectEmailUserLogin($data['email']);

        if ($isNewEmail) {
            $registerStatus = Singleton::getFacade()->getUserInstance()->insertUserLogin($data);
        }


        echo json_encode($registerStatus);
    }

}