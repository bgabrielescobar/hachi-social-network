<?php

namespace App\Helpers\Database\Tables\Base;

use PDO;
use PDOException;
use App\Config\Settings;

class PDOClass {

    /**
     * Single connection shared by every table class.
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
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        try {
            if (Settings::get('DB_DRIVER') == 'sqlite') {
                self::$connection = self::connectSqlite($options);
            } else {
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
            error_log('Database connection error: ' . $e->getMessage());
            http_response_code(500);
            die('Database connection error.');
        }

        return self::$connection;
    }

    /**
     * SQLite is meant for local development: the database file and its
     * tables are created automatically on the first connection.
     */
    private static function connectSqlite(array $options): PDO
    {
        $root = dirname(__DIR__, 5);
        $path = $root . '/' . (Settings::get('DB_PATH') ?: 'database/hachi.sqlite');

        $pdo = new PDO('sqlite:' . $path, null, null, $options);
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->exec(file_get_contents($root . '/database/schema.sqlite.sql'));

        return $pdo;
    }

}
