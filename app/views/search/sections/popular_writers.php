<div class="writers-grid-container">
    <div class="writers-grid">
        <?php foreach ($popularWriters as $writer): ?>
            <div class="writer-card">
                <img class="writer-avatar" src="<?= htmlspecialchars($writer['user_avatar'] ?: '/templates/images/profile.jpg') ?>" alt="<?= htmlspecialchars($writer['user_login']) ?>">
                <div class="writer-info">
                    <h3><?= htmlspecialchars($writer['user_login']) ?></h3>
                    <p><?= htmlspecialchars($writer['user_specialisation']) ?></p>
                    <p class="stats">
                        <?= $translations['followers']; ?>: <?= htmlspecialchars($writer['followers_count']) ?>
                    </p>
                    <a class="profile-link" href="/profile/<?= urlencode($writer['user_login']) ?>">
                        <?= $translations['view_profile']; ?>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
