<?php

namespace App\Helpers\Security;

class CryptoManager {

    private static $instance;

    private $key = "M99()=dbfh92";

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new CryptoManager();
        }
        return self::$instance;
    }

    public function Encrypt($data) {
        return openssl_encrypt($data, 'AES-256-CBC', $this->key);
    }

    public function Decrypt($encryptedData) {
        return openssl_decrypt($encryptedData, 'AES-256-CBC', $this->key);
    }
}