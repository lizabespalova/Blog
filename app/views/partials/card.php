<?php
require_once 'app/services/helpers/switch_language.php';
?>
<div class="card-container">
    <div class="card">
        <div class="card-actions">
            <div class="dropdown">
                <?php
                $user = $_SESSION['user'] ?? null;
                if ($user && $article['user_id'] == $user['user_id']):
                    ?>
                    <button class="dropdown-toggle">⋮</button>
                        <div class="dropdown-menu">
                            <div class="dropdown-menu">
                                <!-- Кнопка для просмотра статистики статьи -->
                                <button class="dropdown-item edit-text" data-article-id="<?php echo htmlspecialchars($article['id']); ?>"
                                        onclick="viewStatistics(<?php echo htmlspecialchars($article['id']); ?>)">
                                    <?php echo $translations['view_statistics']; ?> <i class="fas fa-chart-bar"></i>
                                </button>

                                <!-- Кнопка удаления репоста -->
                                <?php if (!empty($article['repost_id'])): ?>
                                    <button class="dropdown-item delete-card" data-repost-id="<?php echo htmlspecialchars($article['repost_id']); ?>"
                                            onclick="deleteRepost(this)">
                                        <?php echo $translations['delete_repost']; ?> <i class="fas fa-trash-alt"></i>
                                    </button>

                                    <!-- Кнопка изменения описания -->
                                    <button class="dropdown-item edit-text"
                                            data-repost-id="<?= htmlspecialchars($article['repost_id']); ?>"
                                            onclick="editRepost(this)">
                                        <?= $translations['change_description']; ?> <i class="fas fa-edit"></i>
                                    </button>

                                <?php endif; ?>
                            </div>
                        </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-image">
            <img src="/<?= htmlspecialchars($article['cover_image'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($article['title'], ENT_QUOTES) ?>">
        </div>
        <div class="card-content">
            <h3 class="card-title">
                <a href="/articles/<?= htmlspecialchars($article['slug'], ENT_QUOTES) ?>">
                    <?= htmlspecialchars($article['title'], ENT_QUOTES) ?>
                </a>
            </h3>
            <p class="card-meta">
                Author: <?= htmlspecialchars($article['author'], ENT_QUOTES) ?> | Date: <?= date('d.m.Y', strtotime($article['created_at'])) ?>
            </p>
            <?php if (!empty($article['repost_id'])): ?>
                <div class="repost-message">
                    <p><?= htmlspecialchars($article['repost_message'] ?? ''); ?></p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>
