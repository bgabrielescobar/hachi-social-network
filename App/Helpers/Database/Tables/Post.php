<?php

namespace App\Helpers\Database\Tables;

use App\Helpers\Database\Tables\Base\PDOClass;

class Post extends PDOClass {

    const TIMELINE_LIMIT = 50;

    public function __construct()
    {
        parent::__construct();
    }

    public function insertPost(int $userId, string $content): bool
    {
        $sql = "INSERT INTO posts (user_id, content, created_at) VALUES (:user_id, :content, :created_at)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'user_id' => $userId,
            'content' => $content,
            'created_at' => gmdate('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Only the author can delete a post. Returns false when nothing was deleted.
     */
    public function deletePost(int $postId, int $userId): bool
    {
        $sql = "DELETE FROM posts WHERE post_id = :post_id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['post_id' => $postId, 'user_id' => $userId]);

        if ($stmt->rowCount() == 0) {
            return false;
        }

        $stmt = $this->pdo->prepare("DELETE FROM likes WHERE post_id = :post_id");
        $stmt->execute(['post_id' => $postId]);

        return true;
    }

    /**
     * Likes the post, or removes the like if it was already given.
     * Returns whether the post ends up liked by the user.
     */
    public function toggleLike(int $postId, int $userId): bool
    {
        $params = ['post_id' => $postId, 'user_id' => $userId];

        $stmt = $this->pdo->prepare("DELETE FROM likes WHERE post_id = :post_id AND user_id = :user_id");
        $stmt->execute($params);

        if ($stmt->rowCount() > 0) {
            return false;
        }

        // Selecting from posts skips the insert when the post does not exist.
        $sql = "INSERT INTO likes (post_id, user_id) SELECT post_id, :user_id FROM posts WHERE post_id = :post_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount() > 0;
    }

    public function countLikes(int $postId): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = :post_id");
        $stmt->execute(['post_id' => $postId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Latest posts, newest first. When $authorId is given only that user's posts are returned.
     */
    public function selectTimeline(int $viewerId, ?int $authorId = null): array
    {
        $params = ['viewer_id' => $viewerId];

        $sql = "SELECT p.post_id, p.user_id, p.content, p.created_at,
                       up.first_name, up.last_name,
                       (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.post_id) AS likes,
                       (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.post_id AND l.user_id = :viewer_id) AS liked
                FROM posts p
                LEFT JOIN user_profile up ON up.user_id = p.user_id";

        if ($authorId !== null) {
            $sql .= " WHERE p.user_id = :author_id";
            $params['author_id'] = $authorId;
        }

        $sql .= " ORDER BY p.post_id DESC LIMIT " . self::TIMELINE_LIMIT;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

}
