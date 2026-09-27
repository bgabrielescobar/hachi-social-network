<?php

namespace App\Controller;

use App\Controller\Base\Controller;
use App\Helpers\Database\Singleton;
use App\Helpers\Session\SessionManager;

class RegisterController extends Controller {

    const MIN_PASSWORD_LENGTH = 6;

    const MAX_NAME_LENGTH = 50;

    public function indexAction()
    {
        $data = $this->readJson();

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

        SessionManager::getInstance()->login($userId, false);

        $this->jsonResponse(['code' => 0]);
    }

    /**
     * Returns the message to show the user, or null when the data is valid.
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
