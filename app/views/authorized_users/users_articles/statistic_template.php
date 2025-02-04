<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/default.min.css">
    <link rel="stylesheet" href="/css/profile/profile_template.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
    <link rel="stylesheet" href="/css/statistics.css">
    <link rel="stylesheet" href="/css/settings/themes.css">
    <link rel="stylesheet" href="/css/settings/font-style.css">
    <link rel="stylesheet" href="/css/settings/font-size.css">

</head>
<body
        class="<?=
        isset($_SESSION['settings']['theme']) && $_SESSION['settings']['theme'] === 'dark' ? 'dark-mode' : '';
        ?>
    <?= isset($_SESSION['settings']['font_style']) ? htmlspecialchars($_SESSION['settings']['font_style']) : 'sans-serif'; ?>"
        style="font-size: <?= isset($_SESSION['settings']['font_size']) ? htmlspecialchars($_SESSION['settings']['font_size']) : '16' ?>px;">

<!-- Header Section -->
<?php include __DIR__ . '/../../../views/base/profile_header.php'; ?>
<!-- Main Content Section -->
<div class="container">
    <h1><?= sprintf($translations['statistics_for'], htmlspecialchars($article['title'])); ?></h1>
    <div class="statistic-item">
        <strong><i class="fas fa-thumbs-up"></i> <?= $translations['likes']; ?>:</strong>
        <span id="likes"><?= htmlspecialchars($statistics['likes']); ?></span>
        <button class="stat" onclick="showList('likes', '<?= htmlspecialchars($article['slug'], ENT_QUOTES, 'UTF-8'); ?>')">
            <?= $translations['view_likes']; ?>
        </button>
    </div>

    <div class="statistic-item">
        <strong><i class="fas fa-thumbs-down"></i> <?= $translations['dislikes']; ?>:</strong>
        <span id="dislikes"><?= htmlspecialchars($statistics['dislikes']); ?></span>
        <button class="stat" onclick="showList('dislikes', '<?= htmlspecialchars($article['slug'], ENT_QUOTES, 'UTF-8'); ?>')">
            <?= $translations['view_dislikes']; ?>
        </button>
    </div>

    <div class="statistic-item">
        <strong><i class="fas fa-eye"></i> <?= $translations['views']; ?>:</strong>
        <span id="views"><?= htmlspecialchars($statistics['views']); ?></span>
    </div>

    <div class="statistic-item">
        <strong><i class="fas fa-user"></i> <?= $translations['author']; ?>:</strong>
        <span><?= htmlspecialchars($article['author']); ?></span>
    </div>

    <div class="statistic-item">
        <strong><i class="fas fa-calendar-alt"></i> <?= $translations['created_at']; ?>:</strong>
        <span><?= htmlspecialchars($article['created_at']); ?></span>
    </div>

    <!-- График лайков и дизлайков -->
    <div class="chart-container">
        <canvas id="likesDislikesChart"></canvas>
    </div>

    <!-- График просмотров -->
    <div class="chart-container">
        <canvas id="viewsChart"></canvas>
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

<!-- Footer Section -->
<?php include __DIR__ . '/../../../views/base/profile_footer.php'; ?>

<script src="/js/authorized_users/menu.js"></script>
<script src="/js/authorized_users/articles/statistic_graphs.js"></script>
<script src="/js/authorized_users/articles/show_module_likes_dislikes_in_statistic.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>
</html>
