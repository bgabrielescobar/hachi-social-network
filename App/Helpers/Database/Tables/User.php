<?php

namespace App\Helpers\Database\Tables;

use App\Helpers\Database\Tables\Base\PDOClass;

class User extends PDOClass{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Creates the account and its profile, returns the new user id.
     */
    public function insertUser(array $userData): int
    {
        $this->pdo->beginTransaction();

        $sql = "INSERT INTO users (email, password) VALUES (:email, :password)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'email' => $userData['email'],
            'password' => password_hash($userData['pass'], PASSWORD_DEFAULT),
        ]);

        $userId = (int) $this->pdo->lastInsertId();

        $sql = "INSERT INTO user_profile (user_id, last_name, first_name) VALUES (:user_id, :last_name, :first_name)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'last_name' => $userData['last_name'],
            'first_name' => $userData['first_name'],
        ]);

        $this->pdo->commit();

        return $userId;
    }

    public function selectEmailUser(string $email): bool
    {
        $filterUserData = [
            'email' => $email,
        ];

        $sql = "SELECT email FROM users WHERE email = :email";
        $smt = $this->pdo->prepare($sql);
        $smt->execute($filterUserData);
        return $smt->fetchColumn() !== false;
    }

    /**
     * Returns the user id when the credentials are valid, false otherwise.
     */
    public function selectLoginUser(string $email, string $password)
    {
        $filterUserData = [
            'email' => $email,
        ];

        $sql = "SELECT user_id, password FROM users WHERE email = :email";
        $smt = $this->pdo->prepare($sql);
        $smt->execute($filterUserData);
        $user = $smt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return (int) $user['user_id'];
        }

        return false;
    }

    public function selectUserById(int $userId)
    {
        $sql = "SELECT u.user_id, u.email, up.first_name, up.last_name
                FROM users u
                LEFT JOIN user_profile up ON up.user_id = u.user_id
                WHERE u.user_id = :user_id";
        $smt = $this->pdo->prepare($sql);
        $smt->execute(['user_id' => $userId]);
        return $smt->fetch();
    }

}
