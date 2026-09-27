<?php

namespace App\Controller;

use App\Controller\Base\Controller;
use App\Helpers\Session\SessionManager;

/**
 * logout.php: cierra la sesión y vuelve al login.
 */
class LogoutController extends Controller
{
    public function indexAction()
    {
        SessionManager::getInstance()->logout();

        header('Location: index.php');
        exit;
    }
}
