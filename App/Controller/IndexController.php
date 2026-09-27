<?php

namespace App\Controller;

use App\Controller\Base\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        // Logged users are sent to home.php by the Bootstrap.
        $this->postController();
    }

}
