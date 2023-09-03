<?php

namespace App\Controller;

use App\Config\Settings; 
use App\Controller\Base\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {

        if ($this->isUserLogged()) {
            header('Location: home.php');
        }

        $this->postController();
    }

    private function isUserLogged(): bool
    {
        return isset($_COOKIE['user_logged']);
    }

}