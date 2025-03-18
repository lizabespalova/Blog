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
<?php include __DIR__ . '/../../views/base/profile_header.php'; ?>

<h1 class="page-title"><?= $translations['your_notifications']; ?></h1>
<div class="notifications-header-actions">
    <?php if (!empty($notifications)): ?>
        <form method="POST" action="/notifications/clear" class="clear-notifications-form" id="clearNotificationsForm">
            <button type="submit" class="btn-clear-notifications">
                <i class="fas fa-trash-alt"></i> <?= $translations['clear_notifications'] ?? 'Clear All Notifications'; ?>
            </button>
        </form>

    <?php endif; ?>
</div>

<div class="notifications-list">
    <?php if (!empty($notifications)): ?>
        <?php foreach ($notifications as $notif): ?>
            <div class="notification-item" data-id="<?= htmlspecialchars($notif['id']) ?>">
                <div class="notification-avatar">
                    <a href="/profile/<?= htmlspecialchars($notif['reactioner_login']) ?>">
                        <img src="<?= htmlspecialchars($notif['reactioner_avatar'] ?? '/templates/images/profile.jpg') ?>" alt="Avatar">
                    </a>
                </div>
                <div class="notification-content">
                    <p class="notification-title"><?= htmlspecialchars($notif['message']) ?></p>
                    <p class="notification-date"><?= htmlspecialchars($notif['created_at']) ?></p>

                    <!-- Кнопки только для подписок с ожидающим статусом -->
                    <?php if ($notif['status'] === 'pending' && $notif['type'] === 'follow_request'): ?>
                    <div class="notification-actions-container">
                    <form class="notification-actions" method="POST" action="/notifications/approve">
                            <input type="hidden" name="notification_id" value="<?= htmlspecialchars($notif['id']) ?>">
                            <input type="hidden" name="follower_id" value="<?= htmlspecialchars($notif['reactioner_id']) ?>">
                            <button class="btn-approve" type="submit">Approve</button>
                        </form>
                        <form class="notification-actions" method="POST" action="/notifications/reject">
                            <input type="hidden" name="notification_id" value="<?= htmlspecialchars($notif['id']) ?>">
                            <input type="hidden" name="follower_id" value="<?= htmlspecialchars($notif['reactioner_id']) ?>">
                            <button class="btn-reject" type="submit">Reject</button>
                        </form>
                    </div>
                    <?php endif; ?>
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
<script src="/js/authorized_users/notifications/delete_notifications.js"></script>
<script src="/js/authorized_users/menu.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>

</body>
</html>
