<?php

 namespace App\Helpers\Database;

 use App\Helpers\Database\Tables\Post;
 use App\Helpers\Database\Tables\User;

 /**
  * Punto de acceso a las clases de cada tabla (patrón Facade).
  *
  * Cada clase se crea la primera vez que se pide y después se reutiliza el mismo objeto.
  * Para una tabla nueva: crea su clase en Tables/ y agrega aquí un método get...Class().
  */
 class Facade {

    private $userInstance;

    private $postInstance;

    /**
     * Consultas de usuarios: App/Helpers/Database/Tables/User.php
     */
    public function getUserClass()
    {
        if (!$this->userInstance instanceof User) {
            $this->userInstance = new User();
        }

        return $this->userInstance;
    }

    /**
     * Consultas de posts, likes y hashtags: App/Helpers/Database/Tables/Post.php
     */
    public function getPostClass()
    {
        if (!$this->postInstance instanceof Post) {
            $this->postInstance = new Post();
        }

        return $this->postInstance;
    }

 }
