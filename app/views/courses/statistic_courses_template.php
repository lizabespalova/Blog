<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Statistics</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/css/profile/profile_template.css">
    <link rel="stylesheet" href="/css/statistics.css">
    <link rel="stylesheet" href="/css/settings/themes.css">
    <link rel="stylesheet" href="/css/settings/font-style.css">
    <link rel="stylesheet" href="/css/settings/font-size.css">
    <link rel="stylesheet" href="/css/cards.css">
    <link rel="stylesheet" href="/css/statistics.css">
    <link rel="stylesheet" href="/css/profile/profile_template.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
</head>
<body
        class="<?= isset($_SESSION['settings']['theme']) && $_SESSION['settings']['theme'] === 'dark' ? 'dark-mode' : ''; ?>
           <?= isset($_SESSION['settings']['font_style']) ? htmlspecialchars($_SESSION['settings']['font_style']) : 'sans-serif'; ?>"
        style="font-size: <?= isset($_SESSION['settings']['font_size']) ? htmlspecialchars($_SESSION['settings']['font_size']) : '16' ?>px;">

<!-- Header -->
<?php include __DIR__ . '/../../views/base/profile_header.php'; ?>

<div class="container">
    <h1><?= sprintf($translations['statistics_for'], htmlspecialchars($course['title'])); ?></h1>

    <div class="statistic-item">
        <strong><i class="fas fa-thumbs-up"></i> <?= $translations['likes']; ?>:</strong>
        <span id="likes"><?= htmlspecialchars($statistics['likes']); ?></span>
        <button class="stat" onclick="showList('likes', '<?= htmlspecialchars($course['course_id'], ENT_QUOTES, 'UTF-8'); ?>', 'course')">
            <?= $translations['view_likes']; ?>
        </button>
    </div>

    <div class="statistic-item">
        <strong><i class="fas fa-thumbs-down"></i> <?= $translations['dislikes']; ?>:</strong>
        <span id="dislikes"><?= htmlspecialchars($statistics['dislikes']); ?></span>
        <button class="stat" onclick="showList('dislikes', '<?= htmlspecialchars($course['course_id'], ENT_QUOTES, 'UTF-8'); ?>', 'course')">
            <?= $translations['view_likes']; ?>
        </button>
    </div>
    <div class="statistic-item">
        <strong><i class="fas fa-user"></i> <?= $translations['author']; ?>:</strong>
        <span><?= htmlspecialchars($user['user_login']); ?></span>
    </div>

    <div class="statistic-item">
        <strong><i class="fas fa-calendar-alt"></i><?= $translations['created_at']; ?>:</strong>
        <span><?= htmlspecialchars($course['created_at']); ?></span>
    </div>

    <div class="statistic-item">
        <h2><i class="fas fa-star"></i><?= $translations['popular_articles']; ?></h2>
        <ul>
            <?php if (!empty($statistics['popular_articles'])): ?>
                <?php foreach ($statistics['popular_articles'] as $article): ?>
                    <li>
                        <?= htmlspecialchars($article['title']); ?>
                        (<?= (int)$article['views']; ?> <?= $translations['views']; ?>
                        <?php if (isset($article['likes'])): ?>, <?= (int)$article['likes']; ?> 👍<?php endif; ?>
                        <?php if (isset($article['dislikes'])): ?>, <?= (int)$article['dislikes']; ?> 👎<?php endif; ?>)
                    </li>
                    <?php include __DIR__ . '/../../views/partials/card.php'; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <li><?= $translations['no_articles']; ?></li>
            <?php endif; ?>
        </ul>
    </div>


    <div class="chart-container">
        <canvas id="likesDislikesChart"></canvas>
    </div>



    <!-- Modal Window -->
    <span id="views"></span>
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modal-title">List</h2>
            <ul id="modal-list"></ul>
        </div>
    </div>

    <!-- График лайков по статьям -->
    <div class="chart-container">
        <canvas id="likesChart"></canvas>
    </div>
    <!-- Модальные окна для списков -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modal-title">List</h2>
            <ul id="modal-list"></ul>
        </div>
    </div>
</div>

<!-- Footer -->
<?php include __DIR__ . '/../../views/base/profile_footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/js/authorized_users/menu.js"></script>
<script>
    window.popularArticles = <?= json_encode($statistics['popular_articles']); ?>;
</script>
<script src="/js/courses/course_statistic.js"></script>
<script src="/js/authorized_users/articles/statistic_graphs.js"></script>
<script src="/js/authorized_users/articles/show_module_likes_dislikes_in_statistic.js"></script>
<script src="/js/authorized_users/articles/repost_article.js"></script>

</body>
</html>