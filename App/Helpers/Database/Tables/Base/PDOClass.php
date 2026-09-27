<?php

namespace App\Helpers\Database\Tables\Base;

use PDO;
use PDOException;
use App\Config\Settings;

/**
 * Clase base de las tablas: abre la conexión a la base de datos con PDO.
 *
 * Las clases de Tables/ (User, Post) heredan de esta y usan $this->pdo para sus consultas.
 * Según DB_DRIVER en el .env se conecta a MySQL (el sitio publicado) o a SQLite (para
 * programar en tu computadora sin instalar MySQL).
 */
class PDOClass {

    /**
     * Una sola conexión compartida por todas las tablas.
     */
    private static $connection;

    protected $pdo;

    public function __construct()
    {
        $this->pdo = self::getConnection();
    }

    private static function getConnection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $options = [
            // Si una consulta falla, PDO lanza una excepción en lugar de fallar en silencio.
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // Las filas llegan como arrays con el nombre de cada columna: $row['email']
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        try {
            if (Settings::get('DB_DRIVER') == 'sqlite') {
                self::$connection = self::connectSqlite($options);
            } else {
                // utf8mb4 permite guardar cualquier carácter, emojis incluidos.
                self::$connection = new PDO(
                'mysql:' .
                    'host=' . Settings::get('HOST') .
                    ';dbname=' . Settings::get('DB_NAME') .
                    ';charset=utf8mb4',
                    Settings::get('USER_NAME'),
                    Settings::get('PASSWORD'),
                    $options
                );
            }
        } catch (PDOException $e) {
            // El mensaje real puede tener datos del servidor: va al log de errores
            // (la terminal si usas "php -S") y el usuario solo ve un aviso genérico.
            error_log('Database connection error: ' . $e->getMessage());
            http_response_code(500);
            die('Database connection error.');
        }

        return self::$connection;
    }

    /**
     * SQLite es para programar en tu computadora: el archivo de la base de datos
     * y sus tablas se crean solos la primera vez.
     */
    private static function connectSqlite(array $options): PDO
    {
        // La carpeta raíz del proyecto está 5 niveles arriba de esta carpeta.
        $root = dirname(__DIR__, 5);
        $path = $root . '/' . (Settings::get('DB_PATH') ?: 'database/hachi.sqlite');

        $pdo = new PDO('sqlite:' . $path, null, null, $options);
        // SQLite no revisa las claves foráneas (FOREIGN KEY) si no se activa.
        $pdo->exec('PRAGMA foreign_keys = ON');
        // El esquema usa CREATE TABLE IF NOT EXISTS: solo crea las tablas que falten, no borra nada.
        $pdo->exec(file_get_contents($root . '/database/schema.sqlite.sql'));

        return $pdo;
    }

}
