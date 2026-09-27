<?php

namespace App\Controller;

use App\Controller\Base\Controller;
use App\Helpers\Database\Singleton;
use App\Helpers\Session\SessionManager;

class PostController extends Controller
{
    const MAX_LENGTH = 280;

    private $userId;

    private $postTable;

    public function indexAction()
    {
        $data = $this->readJson();

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

    private function create(string $content): void
    {
        $length = mb_strlen($content);

        if ($length == 0 || $length > self::MAX_LENGTH) {
            $this->jsonError("Posts must have between 1 and " . self::MAX_LENGTH . " characters");
        }

        $this->postTable->insertPost($this->userId, $content);

        $this->jsonResponse(['code' => 0]);
    }

    private function delete(int $postId): void
    {
        if (!$this->postTable->deletePost($postId, $this->userId)) {
            $this->jsonError("You can only delete your own posts");
        }

        $this->jsonResponse(['code' => 0]);
    }

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
