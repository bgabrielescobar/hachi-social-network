<?php

 namespace App\Helpers\Database;

 use App\Helpers\Database\Tables\User;

 class Facade {

    private $userInstance;

    function __construct()
    {
        $this->userInstance = new User();    
    }

    public function getUserClass()
    {
        return $this->userInstance;
    }

 }