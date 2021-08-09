<?php

 namespace App\Helpers\Database;

 class Facade {

    private $userInstance;

    function __construct()
    {
        $this->userInstance = new User();    
    }

    public function getUserInstance()
    {
        return $this->userInstance;
    }

 }