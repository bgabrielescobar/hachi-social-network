<?php

namespace App\Controller;

use App\Controller\Base\Controller;
use App\Helpers\Database\Singleton;
use App\Helpers\Hashtag\Hashtag;
use App\Helpers\Session\SessionManager;

/**
 * post.php: endpoint JSON para las acciones sobre los posts.
 *
 * public/js-min/Home.min.js envía un JSON con la acción a realizar:
 *   {action: 'create', content: 'Hola #mundo'}   publica un post
 *   {action: 'delete', post_id: 5}               borra un post propio
 *   {action: 'like',   post_id: 5}               da o quita un like
 */
class PostController extends Controller
{
    /**
     * Máximo de caracteres de un post, como en Twitter.
     * El mismo número está en public/js-min/Home.min.js (MAX_POST_LENGTH).
     */
    const MAX_LENGTH = 280;

    private $userId;

    private $postTable;

    public function indexAction()
    {
        $data = $this->readJson();

        // El Bootstrap ya comprobó que hay sesión, así que siempre hay un usuario.
        $this->userId = SessionManager::getInstance()->getUserId();
        $this->postTable = Singleton::getFacade()->getPostClass();

        switch ($data['action'] ?? '') {
            case 'create':
                $this->create(trim((string) ($data['content'] ?? '')));
                break;
            case 'delete':
                $this->delete((int) ($data['post_id'] ?? 0));
                break;
            case 'like':
                $this->like((int) ($data['post_id'] ?? 0));
                break;
            default:
                $this->jsonError('Unknown action', 400);
        }
    }

    /**
     * Publica el post y guarda sus hashtags para las tendencias.
     */
    private function create(string $content): void
    {
        // mb_strlen cuenta caracteres (un emoji = 1); strlen contaría bytes (un emoji = 4).
        $length = mb_strlen($content);

        // El JavaScript ya lo comprueba, pero el servidor nunca debe confiar en el navegador.
        if ($length == 0 || $length > self::MAX_LENGTH) {
            $this->jsonError("Posts must have between 1 and " . self::MAX_LENGTH . " characters");
        }

        $this->postTable->insertPost($this->userId, $content, Hashtag::extract($content));

        $this->jsonResponse(['code' => 0]);
    }

    /**
     * Borra el post solo si es del usuario que lo pide.
     */
    private function delete(int $postId): void
    {
        if (!$this->postTable->deletePost($postId, $this->userId)) {
            $this->jsonError("You can only delete your own posts");
        }

        $this->jsonResponse(['code' => 0]);
    }

    /**
     * Da o quita el like y responde cómo quedó: {liked: true/false, likes: total}.
     */
    private function like(int $postId): void
    {
        $liked = $this->postTable->toggleLike($postId, $this->userId);

        $this->jsonResponse([
            'code' => 0,
            'liked' => $liked,
            'likes' => $this->postTable->countLikes($postId),
        ]);
    }
}
