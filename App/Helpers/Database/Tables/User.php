<?php

namespace App\Helpers\Database\Tables;

use App\Helpers\Database\Tables\Base\PDOClass;

/**
 * Consultas de las tablas users (email y contraseña) y user_profile (nombre).
 *
 * Todas las consultas usan prepare() + execute() con parámetros (:email, :user_id...):
 * así los datos del usuario nunca se mezclan con el SQL (ver "Inyección SQL" en docs/5-seguridad.md).
 */
class User extends PDOClass{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Crea la cuenta y su perfil. Devuelve el id del usuario nuevo.
     */
    public function insertUser(array $userData): int
    {
        // Transacción: los dos INSERT se guardan juntos o ninguno,
        // así nunca queda un usuario sin perfil.
        $this->pdo->beginTransaction();

        // password_hash no guarda la contraseña, sino un "hash" que no se puede revertir.
        $sql = "INSERT INTO users (email, password) VALUES (:email, :password)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'email' => $userData['email'],
            'password' => password_hash($userData['pass'], PASSWORD_DEFAULT),
        ]);

        // El id que la base de datos le asignó al usuario recién creado.
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

    /**
     * ¿Ya existe una cuenta con este email?
     */
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
     * Devuelve el id del usuario si el email y la contraseña son correctos, o false.
     */
    public function selectLoginUser(string $email, string $password)
    {
        $filterUserData = [
            'email' => $email,
        ];

        // Se busca solo por email y la contraseña se compara en PHP con password_verify:
        // cada hash tiene su propia "sal" aleatoria, así que no se puede comparar en SQL.
        $sql = "SELECT user_id, password FROM users WHERE email = :email";
        $smt = $this->pdo->prepare($sql);
        $smt->execute($filterUserData);
        $user = $smt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return (int) $user['user_id'];
        }

        return false;
    }

    /**
     * Datos de un usuario con su nombre (user_id, email, first_name, last_name), o false si no existe.
     */
    public function selectUserById(int $userId)
    {
        // LEFT JOIN: devuelve el usuario aunque no tenga fila en user_profile.
        $sql = "SELECT u.user_id, u.email, up.first_name, up.last_name
                FROM users u
                LEFT JOIN user_profile up ON up.user_id = u.user_id
                WHERE u.user_id = :user_id";
        $smt = $this->pdo->prepare($sql);
        $smt->execute(['user_id' => $userId]);
        return $smt->fetch();
    }

}
