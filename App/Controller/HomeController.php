<?php

namespace App\Controller;

use App\Helpers\Cookie\CookieManager;

class HomeController
{
    public function indexAction()
    {
        $credential = CookieManager::getInstance()->getLoginCookie();
        echo print_r($credential,1);
    }
}