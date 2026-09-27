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
        $postTable = Singleton::getFacade()->getPostClass();

        // home.php?user=ID shows only the posts of that user.
        $authorId = isset($_GET['user']) ? (int) $_GET['user'] : null;
        $author = $authorId ? $userTable->selectUserById($authorId) : null;

        // home.php?tag=php shows only the posts with #php.
        $tag = isset($_GET['tag']) && is_string($_GET['tag']) ? mb_strtolower(ltrim(trim($_GET['tag']), '#')) : null;

        if (($authorId !== null && !$author) || $tag === '') {
            header('Location: home.php');
            exit;
        }

        $this->data = [
            'user' => $userTable->selectUserById($userId),
            'author' => $author,
            'tag' => $tag,
            'posts' => $postTable->selectTimeline($userId, $authorId, $tag),
            'trends' => $postTable->selectWeeklyTrends(),
        ];

        $this->postController();
    }
}
