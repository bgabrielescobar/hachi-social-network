<?php

namespace App\Controller;

use App\Controller\Base\Controller;

/**
 * index.php: muestra los formularios de login y registro.
 */
class IndexController extends Controller
{
    public function indexAction()
    {
        // Si ya hay sesión, el Bootstrap mandó al usuario a home.php antes de llegar aquí.
        $this->postController();
    }

}
