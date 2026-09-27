<?php

 namespace App\Helpers\Database;

 use App\Helpers\Database\Tables\Post;
 use App\Helpers\Database\Tables\User;

 class Facade {

    private $userInstance;

    private $postInstance;

    public function getUserClass()
    {
        if (!$this->userInstance instanceof User) {
            $this->userInstance = new User();
        }

        return $this->userInstance;
    }

    public function getPostClass()
    {
        if (!$this->postInstance instanceof Post) {
            $this->postInstance = new Post();
        }

        return $this->postInstance;
    }

 }
