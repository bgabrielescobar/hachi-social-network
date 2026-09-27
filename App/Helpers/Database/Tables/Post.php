<?php

namespace App\Helpers\Database\Tables;

use App\Helpers\Database\Tables\Base\PDOClass;

class Post extends PDOClass {

    const TIMELINE_LIMIT = 50;

    const TRENDS_DAYS = 7;

    const TRENDS_LIMIT = 5;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Saves the post and its hashtags (lowercase, without the "#").
     */
    public function insertPost(int $userId, string $content, array $tags): void
    {
        $this->pdo->beginTransaction();

        $sql = "INSERT INTO posts (user_id, content, created_at) VALUES (:user_id, :content, :created_at)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'content' => $content,
            'created_at' => gmdate('Y-m-d H:i:s'),
        ]);

        $postId = (int) $this->pdo->lastInsertId();

        $stmt = $this->pdo->prepare("INSERT INTO post_hashtags (post_id, tag) VALUES (:post_id, :tag)");
        foreach ($tags as $tag) {
            $stmt->execute(['post_id' => $postId, 'tag' => $tag]);
        }

        $this->pdo->commit();
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

        foreach (['likes', 'post_hashtags'] as $table) {
            $stmt = $this->pdo->prepare("DELETE FROM $table WHERE post_id = :post_id");
            $stmt->execute(['post_id' => $postId]);
        }

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
     * Latest posts, newest first. $authorId and $tag keep only the posts of that user / with that hashtag.
     */
    public function selectTimeline(int $viewerId, ?int $authorId = null, ?string $tag = null): array
    {
        $params = ['viewer_id' => $viewerId];

        $sql = "SELECT p.post_id, p.user_id, p.content, p.created_at,
                       up.first_name, up.last_name,
                       (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.post_id) AS likes,
                       (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.post_id AND l.user_id = :viewer_id) AS liked
                FROM posts p
                LEFT JOIN user_profile up ON up.user_id = p.user_id";

        $where = [];

        if ($authorId !== null) {
            $where[] = "p.user_id = :author_id";
            $params['author_id'] = $authorId;
        }

        if ($tag !== null) {
            $where[] = "p.post_id IN (SELECT post_id FROM post_hashtags WHERE tag = :tag)";
            $params['tag'] = $tag;
        }

        if ($where) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sql .= " ORDER BY p.post_id DESC LIMIT " . self::TIMELINE_LIMIT;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Most used hashtags of the last 7 days with their number of posts.
     * On a tie the hashtag used most recently goes first.
     */
    public function selectWeeklyTrends(): array
    {
        $sql = "SELECT h.tag, COUNT(*) AS posts
                FROM post_hashtags h
                JOIN posts p ON p.post_id = h.post_id
                WHERE p.created_at >= :since
                GROUP BY h.tag
                ORDER BY posts DESC, MAX(p.post_id) DESC
                LIMIT " . self::TRENDS_LIMIT;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['since' => gmdate('Y-m-d H:i:s', time() - self::TRENDS_DAYS * 86400)]);
        return $stmt->fetchAll();
    }

}
