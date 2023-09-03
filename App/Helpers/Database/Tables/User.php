<?php

namespace App\Helpers\Database\Tables;

use App\Config\Settings;
use Cassandra\Set;
use PDO;
use App\Helpers\Database\Tables\Base\PDOClass;

class User extends PDOClass{

    public function __construct()
    {
        parent::__construct();        
    }

    public function insertUser(array $userData): bool
    {
        $passEncrypt = Settings::get('SECRET_ENCRYPT_PASS');

        $filterUserData = [
            'email' => $userData['email'],
            'password' => $userData['pass'],
            'pencrypt' => $passEncrypt
        ];

        $sql = "INSERT INTO users (email, password) VALUES (:email, HEX(AES_ENCRYPT(:password, :pencrypt)))";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($filterUserData);
    }

    public function selectEmailUser(string $email): bool
    {
        $filterUserData = [
            'email' => $email,
        ];

        $sql = "SELECT email FROM users WHERE email = :email";
        $smt = $this->pdo->prepare($sql);
        $smt->execute($filterUserData);
        return $smt->fetchColumn();
    }

    public function insertUserName(array $userData)
    {
        $userData = [
            'first_name' => $userData['first_name'],
            'last_name' => $userData['last_name'],
            'user_id' => $this->pdo->lastInsertId()
        ];

        $sql = "INSERT INTO user_profile (user_id, last_name, first_name) VALUES (:user_id, :last_name, :first_name)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($userData);
    }

    public function selectLoginUser(string $email, string $password)
    {
        $passEncrypt = Settings::get('SECRET_ENCRYPT_PASS');

        $filterUserData = [
            'email' => $email,
            'password' => $password,
            'pencrypt' => $passEncrypt
        ];

        $sql = "SELECT user_id FROM users WHERE email = :email AND password = (SELECT HEX(AES_ENCRYPT(:password, :pencrypt)) FROM users) ";
        $smt = $this->pdo->prepare($sql);
        $smt->execute($filterUserData);
        return $smt->fetchColumn();
    }


}