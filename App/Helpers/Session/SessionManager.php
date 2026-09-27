<?php

namespace App\Helpers\Session;

/**
 * Recuerda qué usuario inició sesión, usando las sesiones de PHP.
 *
 * Cómo funciona una sesión: PHP guarda los datos ($_SESSION) en el servidor y le da al
 * navegador una cookie (PHPSESSID) con un código aleatorio. En cada petición el navegador
 * envía esa cookie y PHP recupera los datos. Aquí solo se guarda el id del usuario:
 * el email y la contraseña nunca salen del servidor.
 *
 * Se usa siempre a través de SessionManager::getInstance().
 */
class SessionManager
{
    /**
     * 30 días en segundos, el tiempo que dura "Remember me".
     */
    const REMEMBER_LIFETIME = 86400 * 30;

    private static $instance;

    public static function getInstance(): SessionManager
    {
        if (!self::$instance) {
            self::$instance = new SessionManager();
        }
        return self::$instance;
    }

    /**
     * Es privado para que solo getInstance() pueda crear el objeto: así session_start()
     * se llama una sola vez por petición.
     */
    private function __construct()
    {
        // PHP borra las sesiones que no se usan después de este tiempo.
        ini_set('session.gc_maxlifetime', (string) self::REMEMBER_LIFETIME);
        // 'lifetime' => 0: por defecto la cookie se borra al cerrar el navegador.
        session_set_cookie_params(['lifetime' => 0] + $this->cookieOptions());
        session_start();
    }

    /**
     * Inicia la sesión del usuario. Con $remember la sesión sigue después de cerrar el navegador.
     */
    public function login(int $userId, bool $remember): void
    {
        // Nuevo código de sesión al iniciar sesión: si alguien conocía el anterior, ya no le sirve.
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;

        if ($remember) {
            // Misma cookie, pero con fecha de vencimiento: dura 30 días.
            setcookie(session_name(), session_id(), ['expires' => time() + self::REMEMBER_LIFETIME] + $this->cookieOptions());
        }
    }

    /**
     * Borra los datos de la sesión y la cookie del navegador.
     */
    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        // Una fecha pasada le dice al navegador que borre la cookie.
        setcookie(session_name(), '', ['expires' => time() - 3600] + $this->cookieOptions());
    }

    /**
     * El id del usuario con sesión iniciada, o null si no hay sesión.
     */
    public function getUserId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    /**
     * Opciones de seguridad de la cookie de sesión:
     *   secure    solo se envía por HTTPS (cuando el sitio usa HTTPS)
     *   httponly  JavaScript no puede leerla, así un ataque XSS no puede robarla
     *   samesite  Lax: el navegador no la envía en peticiones POST que vienen de otras webs (CSRF)
     */
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
