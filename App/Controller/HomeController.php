<?php

namespace App\Controller;

use App\Controller\Base\Controller;
use App\Helpers\Database\Singleton;
use App\Helpers\Session\SessionManager;

class HomeController extends Controller
{
    public function indexAction()
    {
        $userId = SessionManager::getInstance()->getUserId();
        $userTable = Singleton::getFacade()->getUserClass();

        // home.php?user=ID shows only the posts of that user.
        $authorId = isset($_GET['user']) ? (int) $_GET['user'] : null;
        $author = $authorId ? $userTable->selectUserById($authorId) : null;

        if ($authorId !== null && !$author) {
            header('Location: home.php');
            exit;
        }

        $this->data = [
            'user' => $userTable->selectUserById($userId),
            'author' => $author,
            'posts' => Singleton::getFacade()->getPostClass()->selectTimeline($userId, $authorId),
        ];

        $this->postController();
    }
}
