<?php

namespace App\Controller\Base;

use App\Config\Settings;
use App\Model\IndexModel;

abstract class Controller {
    
    const MODEL = "Module";

    protected function postController($data): void
    {
        $moduleObject = 'App\Module\\' . Settings::getSetting('controller') . self::MODEL;
        (new $moduleObject())->indexModel($data);

    }

}