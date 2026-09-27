<?php

namespace App\Helpers\Database\Tables;

use App\Helpers\Database\Tables\Base\PDOClass;

/**
 * Consultas de las tablas posts, likes y post_hashtags.
 *
 * Las fechas se guardan en UTC (hora universal) con el formato "2026-09-27 18:00:00",
 * así no dependen de la zona horaria del servidor.
 */
class Post extends PDOClass {

    /**
     * Cuántos posts muestra el timeline.
     */
    const TIMELINE_LIMIT = 50;

    /**
     * Las tendencias cuentan los posts de los últimos 7 días.
     */
    const TRENDS_DAYS = 7;

    /**
     * Cuántos hashtags muestra "Trends this week".
     */
    const TRENDS_LIMIT = 5;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Guarda el post y sus hashtags (en minúsculas y sin el "#").
     */
    public function insertPost(int $userId, string $content, array $tags): void
    {
        // Transacción: el post y sus hashtags se guardan juntos o nada.
        $this->pdo->beginTransaction();

        $sql = "INSERT INTO posts (user_id, content, created_at) VALUES (:user_id, :content, :created_at)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'content' => $content,
            'created_at' => gmdate('Y-m-d H:i:s'),
        ]);

        $postId = (int) $this->pdo->lastInsertId();

        // Una fila por hashtag: "Hola #php #mysql" -> (5, 'php') y (5, 'mysql')
        $stmt = $this->pdo->prepare("INSERT INTO post_hashtags (post_id, tag) VALUES (:post_id, :tag)");
        foreach ($tags as $tag) {
            $stmt->execute(['post_id' => $postId, 'tag' => $tag]);
        }

        $this->pdo->commit();
    }

    /**
     * Solo el autor puede borrar su post. Devuelve false si no se borró nada.
     */
    public function deletePost(int $postId, int $userId): bool
    {
        // "AND user_id = :user_id" es lo que impide borrar posts de otras personas.
        $sql = "DELETE FROM posts WHERE post_id = :post_id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['post_id' => $postId, 'user_id' => $userId]);

        // rowCount(): cuántas filas borró la consulta.
        if ($stmt->rowCount() == 0) {
            return false;
        }

        // También sus likes y hashtags. La base de datos ya lo hace sola (ON DELETE CASCADE),
        // pero así funciona aunque las claves foráneas no estén activas.
        foreach (['likes', 'post_hashtags'] as $table) {
            $stmt = $this->pdo->prepare("DELETE FROM $table WHERE post_id = :post_id");
            $stmt->execute(['post_id' => $postId]);
        }

        return true;
    }

    /**
     * Da like al post, o lo quita si ya lo tenía.
     * Devuelve true si el post queda con like del usuario.
     */
    public function toggleLike(int $postId, int $userId): bool
    {
        $params = ['post_id' => $postId, 'user_id' => $userId];

        // Primero intentamos quitar el like: si se borró una fila, el usuario ya lo había dado.
        $stmt = $this->pdo->prepare("DELETE FROM likes WHERE post_id = :post_id AND user_id = :user_id");
        $stmt->execute($params);

        if ($stmt->rowCount() > 0) {
            return false;
        }

        // Si no lo tenía, lo agregamos. Tomar el post_id de la tabla posts hace que
        // no se inserte nada cuando el post no existe.
        $sql = "INSERT INTO likes (post_id, user_id) SELECT post_id, :user_id FROM posts WHERE post_id = :post_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount() > 0;
    }

    /**
     * Total de likes de un post.
     */
    public function countLikes(int $postId): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = :post_id");
        $stmt->execute(['post_id' => $postId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Los últimos posts, del más nuevo al más viejo.
     * Con $authorId solo los de ese usuario; con $tag solo los que tienen ese hashtag.
     *
     * Cada fila trae: post_id, user_id, content, created_at, first_name, last_name,
     * likes (total de likes) y liked (1 si $viewerId ya le dio like, 0 si no).
     */
    public function selectTimeline(int $viewerId, ?int $authorId = null, ?string $tag = null): array
    {
        $params = ['viewer_id' => $viewerId];

        // Las dos "subconsultas" (SELECT dentro de SELECT) cuentan los likes de cada post.
        $sql = "SELECT p.post_id, p.user_id, p.content, p.created_at,
                       up.first_name, up.last_name,
                       (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.post_id) AS likes,
                       (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.post_id AND l.user_id = :viewer_id) AS liked
                FROM posts p
                LEFT JOIN user_profile up ON up.user_id = p.user_id";

        // Los filtros se agregan solo si se pidieron.
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

        // Los ids crecen con cada post nuevo: ordenar por id es ordenar por fecha.
        $sql .= " ORDER BY p.post_id DESC LIMIT " . self::TIMELINE_LIMIT;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Los hashtags usados en más posts durante los últimos 7 días, con cuántos posts tiene cada uno:
     * [['tag' => 'php', 'posts' => 3], ['tag' => 'hachi', 'posts' => 2], ...]
     * Si hay empate, primero el que se usó más recientemente.
     */
    public function selectWeeklyTrends(): array
    {
        // JOIN      une cada hashtag con su post, para saber la fecha
        // WHERE     deja solo los posts de los últimos 7 días
        // GROUP BY  junta las filas del mismo hashtag y COUNT(*) las cuenta
        // ORDER BY  los más usados primero; en empate, el del post más nuevo (MAX(post_id))
        $sql = "SELECT h.tag, COUNT(*) AS posts
                FROM post_hashtags h
                JOIN posts p ON p.post_id = h.post_id
                WHERE p.created_at >= :since
                GROUP BY h.tag
                ORDER BY posts DESC, MAX(p.post_id) DESC
                LIMIT " . self::TRENDS_LIMIT;

        // Fecha de hace 7 días: ahora menos 7 * 86400 segundos (86400 = segundos de un día).
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['since' => gmdate('Y-m-d H:i:s', time() - self::TRENDS_DAYS * 86400)]);
        return $stmt->fetchAll();
    }

}
