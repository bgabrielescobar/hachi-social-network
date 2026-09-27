<?php
/**
 * Timeline, página de usuario y página de hashtag (home.php).
 *
 * Datos que llegan de HomeController y HomeModule:
 *   $data['user']    el usuario con sesión: user_id, name, initials, color
 *   $data['author']  el usuario de home.php?user=ID, o null
 *   $data['tag']     el hashtag de home.php?tag=nombre, o null
 *   $data['posts']   lista de posts: post_id, user_id, name, initials, color, date,
 *                    content_html, likes, liked, is_mine, created_at
 *   $data['trends']  tendencias de la semana: tag, posts, label
 *
 * Todo texto escrito por usuarios pasa por htmlspecialchars() antes de mostrarse,
 * para que nadie pueda meter HTML o JavaScript en la página (XSS, ver docs/5-seguridad.md).
 * content_html ya viene escapado por Hashtag::toHtml().
 */
?>
<?php /* Barra superior: logo, usuario y "Log out" */ ?>
<header class="topbar">
    <div class="topbar-inner">
        <a href="home.php" class="brand">🐶 Hachi</a>
        <div class="topbar-user">
            <a href="home.php?user=<?php echo $data['user']['user_id'] ?>" class="topbar-name">
                <span class="avatar avatar-sm" style="background: <?php echo $data['user']['color'] ?>"><?php echo htmlspecialchars($data['user']['initials']) ?></span>
                <span class="topbar-name-text"><?php echo htmlspecialchars($data['user']['name']) ?></span>
            </a>
            <a href="logout.php" class="logout-link">Log out</a>
        </div>
    </div>
</header>

<div class="layout">
<main class="timeline">
    <?php /* Arriba del timeline: el perfil, el hashtag o la caja para escribir un post */ ?>
    <?php if ($data['author']): ?>
        <section class="card profile-header">
            <a href="home.php" class="back-link">&larr; Back to timeline</a>
            <span class="avatar avatar-lg" style="background: <?php echo $data['author']['color'] ?>"><?php echo htmlspecialchars($data['author']['initials']) ?></span>
            <h1><?php echo htmlspecialchars($data['author']['name']) ?></h1>
        </section>
    <?php elseif ($data['tag'] !== null): ?>
        <section class="card profile-header">
            <a href="home.php" class="back-link">&larr; Back to timeline</a>
            <h1 class="hashtag-title">#<?php echo htmlspecialchars($data['tag']) ?></h1>
        </section>
    <?php else: ?>
        <form class="card compose" id="compose-form">
            <span class="avatar" style="background: <?php echo $data['user']['color'] ?>"><?php echo htmlspecialchars($data['user']['initials']) ?></span>
            <div class="compose-body">
                <textarea name="content" rows="3" placeholder="What's happening?" aria-label="Write a post"></textarea>
                <div class="compose-footer">
                    <span class="char-counter">280</span>
                    <button type="submit" class="btn" disabled>Post</button>
                </div>
            </div>
        </form>
    <?php endif ?>

    <?php if (empty($data['posts'])): ?>
        <?php if ($data['author']): ?>
            <p class="empty">No posts yet.</p>
        <?php elseif ($data['tag'] !== null): ?>
            <p class="empty">No posts with #<?php echo htmlspecialchars($data['tag']) ?> yet.</p>
        <?php else: ?>
            <p class="empty">No posts yet. Be the first to say something!</p>
        <?php endif ?>
    <?php endif ?>

    <?php /* data-post-id: Home.min.js lo lee para saber a qué post dar like o borrar */ ?>
    <?php foreach ($data['posts'] as $post): ?>
        <article class="card post" data-post-id="<?php echo $post['post_id'] ?>">
            <span class="avatar" style="background: <?php echo $post['color'] ?>"><?php echo htmlspecialchars($post['initials']) ?></span>
            <div class="post-body">
                <div class="post-header">
                    <a href="home.php?user=<?php echo $post['user_id'] ?>" class="post-author"><?php echo htmlspecialchars($post['name']) ?></a>
                    <span class="post-date" title="<?php echo $post['created_at'] ?> UTC">· <?php echo $post['date'] ?></span>
                    <?php if ($post['is_mine']): ?>
                        <button type="button" class="post-delete" title="Delete post">Delete</button>
                    <?php endif ?>
                </div>
                <p class="post-content"><?php echo $post['content_html'] ?></p>
                <button type="button" class="like-btn<?php echo $post['liked'] ? ' liked' : '' ?>" aria-label="Like">
                    <span class="like-icon">&#9829;&#xFE0E;</span>
                    <span class="like-count"><?php echo $post['likes'] ?></span>
                </button>
            </div>
        </article>
    <?php endforeach ?>
</main>

<?php /* Tendencias de la semana (en el celular se muestran arriba del timeline) */ ?>
<aside class="sidebar">
    <section class="card trends">
        <h2>Trends this week</h2>
        <?php if (empty($data['trends'])): ?>
            <p class="trends-empty">No trends yet. Add a #hashtag to your posts to start one.</p>
        <?php else: ?>
            <ol class="trend-list">
                <?php foreach ($data['trends'] as $index => $trend): ?>
                    <li>
                        <a href="home.php?tag=<?php echo urlencode($trend['tag']) ?>" class="trend<?php echo $trend['tag'] === $data['tag'] ? ' active' : '' ?>">
                            <span class="trend-rank"><?php echo $index + 1 ?> · Trending</span>
                            <span class="trend-tag">#<?php echo htmlspecialchars($trend['tag']) ?></span>
                            <span class="trend-count"><?php echo $trend['label'] ?></span>
                        </a>
                    </li>
                <?php endforeach ?>
            </ol>
        <?php endif ?>
    </section>
</aside>
</div>
