<?php

namespace App\Controller\Base;

use App\Config\Settings;
use App\Model\IndexModel;

abstract class Controller {
    
    const MODEL = "Module";

    const PREFIX_MODULE = 'App\Module\\';

    protected $data = null;

    protected function postController(): void
    {
        $moduleObject =  Controller::PREFIX_MODULE . Settings::get('controller') . self::MODEL;
        (new $moduleObject())->indexModel($this->data);
    }

}