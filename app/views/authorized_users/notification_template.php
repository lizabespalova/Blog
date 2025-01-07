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
    <link rel="stylesheet" href="/css/profile/notification.css">
</head>
<body>
<!-- Header Section -->
<?php include __DIR__ . '/../../views/base/profile_header.php'; ?>

<h1 class="page-title">Your Notifications</h1>

<div class="notifications-list">
    <?php if (!empty($notifications)): ?>
        <?php foreach ($notifications as $notif): ?>
            <div class="notification-item" data-id="<?= htmlspecialchars($notif['id']) ?>" onclick="highlightNotification(<?= htmlspecialchars($notif['id']) ?>)">
                <div class="notification-avatar">
                    <a href="/profile/<?= htmlspecialchars($notif['reactioner_login']) ?>">
                        <img src="<?= htmlspecialchars($notif['reactioner_avatar'] ?? '/templates/images/profile.jpg') ?>" alt="Avatar">
                    </a>
                </div>
                <div class="notification-content">
                    <p class="notification-title"><?= htmlspecialchars($notif['message']) ?></p>
                    <p class="notification-date"><?= htmlspecialchars($notif['created_at']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-notifications">You don't have notifications.</p>
    <?php endif; ?>
</div>


<!-- Footer Section -->
<?php include __DIR__ . '/../../views/base/profile_footer.php'; ?>

<script src="/js/async_tasks/send_notifications.js"></script>
<script src="/js/authorized_users/menu.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>

</body>
</html>
