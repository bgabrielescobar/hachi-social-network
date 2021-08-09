<?php

namespace App\Helpers\Database;

use App\Helpers\Database\Base\PDOClass;

class User extends PDOClass{

    public function __construct()
    {
        parent::__construct();        
    }

    public function insertUserLogin(array $userData) 
    {
        $filterUserData = [
            'email' => $userData['email'],
            'password' => $userData['pass'],
        ];

        $sql = "INSERT INTO user_login (email, password) VALUES (:email, :password)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($filterUserData);
    }

    public function selectEmailUserLogin(string $email)
    {
        $filterUserData = [
            'email' => $email,
        ];

        $sql = "SELECT email FROM user_login WHERE email = :email";
        $smt = $this->pdo->prepare($sql);
        $smt->execute($filterUserData);
        echo print_r("Jiji" . $smt->fetch(), 1);
    }

    public function insertUserProfile(array $userData)
    {
        /*$userData = [
            'name' => $name,
            'surname' => $surname,
            'sex' => $sex,
        ];*/

        $sql = "INSERT INTO user_profile (last_name, first_name, address, quote, city, age) VALUES (:last_name, :first_name, :address, :quote, :city, :age)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($userData);
    }

    public function updateUserProfile()
    {

    }


}