<?php

namespace App\Controller;

use App\Controller\Base\Controller;
use App\Helpers\Session\SessionManager;

class LogoutController extends Controller
{
    public function indexAction()
    {
        SessionManager::getInstance()->logout();

        header('Location: index.php');
        exit;
    }
}
