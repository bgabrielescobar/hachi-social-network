<?php

namespace App\Helpers\Session;

/**
 * Keeps the logged user in a PHP session. Only the user id is stored,
 * credentials never leave the server.
 */
class SessionManager
{
    const REMEMBER_LIFETIME = 86400 * 30;

    private static $instance;

    public static function getInstance(): SessionManager
    {
        if (!self::$instance) {
            self::$instance = new SessionManager();
        }
        return self::$instance;
    }

    private function __construct()
    {
        ini_set('session.gc_maxlifetime', (string) self::REMEMBER_LIFETIME);
        session_set_cookie_params(['lifetime' => 0] + $this->cookieOptions());
        session_start();
    }

    public function login(int $userId, bool $remember): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;

        if ($remember) {
            // Keep the session cookie after the browser is closed.
            setcookie(session_name(), session_id(), ['expires' => time() + self::REMEMBER_LIFETIME] + $this->cookieOptions());
        }
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        setcookie(session_name(), '', ['expires' => time() - 3600] + $this->cookieOptions());
    }

    public function getUserId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    private function cookieOptions(): array
    {
        return [
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ];
    }
}
