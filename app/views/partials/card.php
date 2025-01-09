<div class="card-container">
    <div class="card">
        <div class="card-actions">
            <div class="dropdown">
                <button class="dropdown-toggle">⋮</button>
                <div class="dropdown-menu">
                    <?php if ($article['user_id'] == $_SESSION['user']['user_id']): ?>
                        <div class="dropdown-menu">
                            <!-- Кнопка для просмотра статистики статьи -->
                            <button class="dropdown-item edit-text" data-article-id="<?php echo htmlspecialchars($article['id']); ?>" onclick="viewStatistics(<?php echo htmlspecialchars($article['id']); ?>)">
                                View article statistics <i class="fas fa-chart-bar"></i>
                            </button>

                            <!-- Кнопка удаления репоста -->
                            <?php if (!empty($article['repost_id'])): ?>
                                <button class="dropdown-item delete-card" data-repost-id="<?php echo htmlspecialchars($article['repost_id']); ?>" onclick="deleteRepost(this)">
                                    Delete <i class="fas fa-trash-alt"></i>
                                </button>

                                <!-- Кнопка изменения описания -->
                                <button class="dropdown-item edit-text" data-article-id="<?php echo htmlspecialchars($article['id']); ?>">
                                    Change description <i class="fas fa-edit"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

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
        </div>
    </div>
</div>
