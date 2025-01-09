<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/default.min.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
    <link rel="stylesheet" href="/css/statistics.css">
</head>
<body>
<!-- Header Section -->
<?php include __DIR__ . '/../../../views/base/profile_header.php'; ?>
<!-- Main Content Section -->
<div class="container">
    <h1>Statistics for "<?php echo htmlspecialchars($article['title']); ?>"</h1>
    <div class="statistic-item">
        <strong><i class="fas fa-thumbs-up"></i> Likes:</strong>
        <span id="likes"><?php echo $statistics['likes']; ?></span>
    </div>
    <div class="statistic-item">
        <strong><i class="fas fa-thumbs-down"></i> Dislikes:</strong>
        <span id="dislikes"><?php echo $statistics['dislikes']; ?></span>
    </div>
    <div class="statistic-item">
        <strong><i class="fas fa-eye"></i> Views:</strong>
        <span id="views"><?php echo $statistics['views']; ?></span>
    </div>
    <div class="statistic-item">
        <strong><i class="fas fa-user"></i> Author:</strong>
        <span><?php echo htmlspecialchars($article['author']); ?></span>
    </div>
    <div class="statistic-item">
        <strong><i class="fas fa-calendar-alt"></i> Created At:</strong>
        <span><?php echo $article['created_at']; ?></span>
    </div>

    <!-- График лайков и дизлайков -->
    <div class="chart-container">
        <canvas id="likesDislikesChart"></canvas>
    </div>

    <!-- График просмотров -->
    <div class="chart-container">
        <canvas id="viewsChart"></canvas>
    </div>
</div>

<!-- Footer Section -->
<?php include __DIR__ . '/../../../views/base/profile_footer.php'; ?>

<script src="/js/authorized_users/menu.js"></script>
<script src="/js/authorized_users/articles/statistic_graphs.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>
</html>
