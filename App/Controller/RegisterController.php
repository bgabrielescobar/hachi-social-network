<?php

namespace App\Controller;

use App\Helpers\Database\Singleton;

class RegisterController {

    /**
     * Message to response the user, @TODO will change to text file content.
     */

    const SUCCESS_BGCOLOR        = "green";
    const SUCCESS_MESSAGE        = "Welcome to Hachi's social network! Enter your account in the login section" ;
    const SUCCESS_TITLE          = "SUCCESS!";

    const FAILED_BGCOLOR = "red";
    const FAILED_MESSAGE = "There was an error with the registration, re-enter your account information";
    const FAILED_TITLE   = "FAILED!";

    public function indexAction()
    {
        $registerStatus = false;

        $data = json_decode(file_get_contents('php://input'), 1);

        $isRegisteredEmail = Singleton::getFacade()->getUserClass()->selectEmailUser($data['email']);

        if (!$isRegisteredEmail) {
            $registerStatus = Singleton::getFacade()->getUserClass()->insertUser($data);
            Singleton::getFacade()->getUserClass()->insertUserName($data);
        }

        $response = [
            'bgcolor' => $registerStatus ? self::SUCCESS_BGCOLOR : self::FAILED_BGCOLOR,
            'message' => $registerStatus ? self::SUCCESS_MESSAGE : self::FAILED_MESSAGE,
            'title' => $registerStatus ? self::SUCCESS_TITLE : self::FAILED_TITLE,
        ];

        echo json_encode($response);
    }

}