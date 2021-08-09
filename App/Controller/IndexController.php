<?php

namespace App\Controller;

use App\Config\Settings; 
use App\Controller\Base\Controller;

class IndexController extends Controller{

    public function indexAction()
    {
        $data = null;
        
        $this->postController($data);
    }

}