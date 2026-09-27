<?php

namespace App\Controller;

use App\Controller\Base\Controller;
use App\Helpers\Database\Singleton;
use App\Helpers\Session\SessionManager;

/**
 * home.php: el timeline con los últimos posts y las tendencias de la semana.
 *
 * La misma página sirve para filtrar:
 *   - home.php?user=3    solo los posts del usuario 3 (su perfil)
 *   - home.php?tag=php   solo los posts con #php
 */
class HomeController extends Controller
{
    public function indexAction()
    {
        // El Bootstrap ya comprobó que hay sesión, así que siempre hay un usuario.
        $userId = SessionManager::getInstance()->getUserId();
        $userTable = Singleton::getFacade()->getUserClass();
        $postTable = Singleton::getFacade()->getPostClass();

        // home.php?user=ID muestra solo los posts de ese usuario.
        $authorId = isset($_GET['user']) ? (int) $_GET['user'] : null;
        $author = $authorId ? $userTable->selectUserById($authorId) : null;

        // home.php?tag=php muestra solo los posts con #php (en minúsculas y sin el "#").
        $tag = isset($_GET['tag']) && is_string($_GET['tag']) ? mb_strtolower(ltrim(trim($_GET['tag']), '#')) : null;

        // Si el usuario no existe o el hashtag está vacío, volvemos al timeline.
        if (($authorId !== null && !$author) || $tag === '') {
            header('Location: home.php');
            exit;
        }

        // HomeModule prepara estos datos y App/View/Home.view.php los muestra.
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
