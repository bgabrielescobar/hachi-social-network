<?php

namespace App\Helpers\Cookie;

use App\Helpers\Security\CryptoManager;

class CookieManager
{
    private static $instance;

    private $userCredential;

    const COOKIE_NAME = "auth";
    const SEPARATOR = "+";

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new CookieManager();
        }
        return self::$instance;
    }

    public function setLoginCookie($data)
    {
        $concatCred = $data['email'] . CookieManager::SEPARATOR . $data['pass'];
        $cookieValue = CryptoManager::getInstance()->Encrypt($concatCred);

        setcookie(CookieManager::COOKIE_NAME, $cookieValue, time() + (86400 * 30), '/');
    }

    public function getLoginCookie()
    {
        if (isset($this->userCredential)) {
            return $this->userCredential;
        }

        if (empty($_COOKIE[CookieManager::COOKIE_NAME])) {
            return false;
        }

        $cookie = $_COOKIE[CookieManager::COOKIE_NAME];
        $decryptCookie = CryptoManager::getInstance()->Decrypt($cookie);
        $credential = explode(CookieManager::SEPARATOR, $decryptCookie);
        $this->userCredential = [
            'email' => $credential[0],
            'pass' => $credential[1]
        ];
        return $this->userCredential;
    }
}