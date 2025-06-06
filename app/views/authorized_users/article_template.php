<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?></title>
    <link rel="stylesheet" href="/css/profile/article_template.css">
    <link rel="stylesheet" href="/css/reactions_buttons.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Подключаем стили SimpleMDE для рендеринга Markdown -->
    <link rel="stylesheet" href="/css/profile/markdown.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.6.0/styles/default.min.css">

    <link rel="stylesheet" href="/css/settings/themes.css">
    <link rel="stylesheet" href="/css/settings/font-style.css">
    <link rel="stylesheet" href="/css/settings/font-size.css">


    <!--    <base href="http://localhost:8080/">-->
</head>
<body
        class="<?=
        isset($_SESSION['settings']['theme']) && $_SESSION['settings']['theme'] === 'dark' ? 'dark-mode' : '';
        ?>
    <?= isset($_SESSION['settings']['font_style']) ? htmlspecialchars($_SESSION['settings']['font_style']) : 'sans-serif'; ?>"
        style="font-size: <?= isset($_SESSION['settings']['font_size']) ? htmlspecialchars($_SESSION['settings']['font_size']) : '16' ?>px;">

<header>
    <?php include __DIR__ . '/../../views/base/profile_header.php'; ?>
</header>

<main class="article-container">
    <article class="article-content">
        <div class="article-card">
            <!-- Карточка автора -->
            <div class="author-card">
                <!-- Аватар -->
                <a href="/profile/<?php echo urlencode($article['author']); ?>" class="author-avatar-wrapper">
                    <?php if (!empty($author_info['user_avatar'])): ?>
                        <img src="<?= htmlspecialchars($author_info['user_avatar']) ?>" alt="Your Avatar">
                    <?php else: ?>
                        <img src="/templates/images/profile.jpg" alt="Default Avatar">
                    <?php endif; ?>                </a>
                <!-- Заголовок статьи -->
                <h2 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h2>
                <?php if ($user !== null): ?>
                    <?php if ($user['user_login'] === $article['author']) : ?>
                        <!-- Кнопки редактирования и удаления только для автора статьи -->
                        <div class="article-actions">
                            <a href="/articles/edit/<?php echo urlencode($article['slug']); ?>" class="btn btn-edit"><?= $translations['edit']?></a>
                            <a href="javascript:void(0);" class="btn btn-delete-article" onclick="confirmDelete(event, '<?php echo urlencode($article['slug']); ?>')"><?= $translations['delete']?></a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <!-- Имя автора ниже аватара -->
            <div class="author-info">
                <a href="/profile/<?php echo urlencode($article['author']); ?>" class="article-author"><?php echo htmlspecialchars($article['author']); ?></a>
            </div>

            <!-- Информация о статье -->
            <div class="article-details">
        <span class="article-date">
            <i class="far fa-calendar-alt"></i> <?php echo htmlspecialchars(date("d M Y", strtotime($article['created_at']))); ?>
        </span>
                <span class="article-views">
            <i class="far fa-eye"></i> <?php echo htmlspecialchars($article['views']); ?>
        </span>
                <span class="article-category">
            <i class="fas fa-folder"></i> <?php echo htmlspecialchars($article['category']); ?>
        </span>
                <span class="article-difficulty">
            <i class="fas fa-signal"></i> <?php echo htmlspecialchars($article['difficulty']); ?>
        </span>
                <span class="article-read-time">
            <i class="fas fa-hourglass-half"></i> <?php echo htmlspecialchars($article['read_time']); ?> min
        </span>
                <span class="article-tags">
            <i class="fas fa-tags"></i> <?php echo $tagsOutput; ?>
        </span>
            </div>

            <!-- Основной контент статьи -->
            <div class="article-text">
                <div id="toast-container"></div>

                <?php if (!empty($parsedContent)): ?>
                    <div id="rendered-content-1">
                        <?php echo $parsedContent; ?>
                    </div>
                <?php else: ?>
                    <p>No content available for this article.</p>
                <?php endif; ?>
            </div>

            <!-- Видеоролик YouTube -->
            <?php if ($youtube_embed_url): ?>
                <div class="youtube-video">
                    <h3>Video for the article</h3>
                    <iframe width="560" height="315" src="<?php echo $youtube_embed_url; ?>"
                            frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                    </iframe>
                </div>
            <?php endif; ?>
            <!-- Разделительная полоска перед комментариями -->
            <hr class="article-divider">

            <!-- Блок с лайками, дизлайками, избранным и репостом -->
            <section class="article-reactions">
                <div class="reaction-buttons">
                    <!-- Лайки -->
                    <button class="btn-like" title="Like" data-url="/articles/react" data-slug="<?php echo htmlspecialchars($article['slug']); ?>" data-user_id="<?php echo htmlspecialchars($user['user_id']); ?>">
                        <i class="fas fa-thumbs-up"></i>
                        <span class="like-count"><?php echo htmlspecialchars($article['likes']); ?></span> <!-- Здесь будет отображаться количество лайков -->
                    </button>

                    <!-- Дизлайки -->
                    <button class="btn-dislike" title="Dislike" data-url="/articles/react" data-slug="<?php echo htmlspecialchars($article['slug']); ?>" data-user_id="<?php echo htmlspecialchars($user['user_id']); ?>">
                        <i class="fas fa-thumbs-down"></i>
                        <span class="dislike-count"><?php echo htmlspecialchars($article['dislikes']); ?></span> <!-- Здесь будет отображаться количество дизлайков -->
                    </button>

                    <?php if ($user !== null): ?>
                    <!-- Избранное-->
                        <button class="btn-favorite <?= $is_favorite ? 'added' : '' ?>"
                                data-article-id="<?= $article['id'] ?>"
                                title="<?= $is_favorite ? 'Remove from Favorites' : 'Add to Favorites' ?>">
                            <i class="fas fa-star"></i>
                        </button>


                        <!-- Кнопка репоста -->
                        <button class="btn-repost" title="Share" onclick="openRepostForm()">
                            <i class="fas fa-share"></i>
                        </button>
                    <?php endif; ?>

                    <!-- Модальное окно для репоста -->
                    <div id="repost-form" class="repost-form-overlay">
                        <div class="repost-form-container">
                            <!-- Крестик для закрытия -->
                            <span class="repost-form-close" onclick="closeRepostForm()">&times;</span>


                            <!-- Поле для ввода сообщения -->
                            <textarea id="repost-message" placeholder="Add a message..." class="repost-input" maxlength="250"></textarea>
                            <div id="repost-char-count">0/250</div>
                            <!-- Кнопки (отправить и отменить) -->
                            <div class="repost-buttons">
                                <input type="hidden" id="user-id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
                                <input type="hidden" id="article-id" value="<?php echo htmlspecialchars($article['id']); ?>">
                                <button onclick="submitRepost()" class="repost-submit">Post to Wall</button>
                            </div>
                        </div>
                    </div>

                    <!-- Поделиться -->
                    <?php include __DIR__ . '/../../views/partials/share_modal_menu.php'; ?>

                </div>
            </section>
            <!-- Блок с комментариями -->
            <div id="comments-section" data-article-slug="<?php echo htmlspecialchars($article['slug']); ?>">
                <h3>Comments <span class="comment-count">(<?php echo $comment_count; ?>)</span></h3>
                <?php include __DIR__ . '/comments_template.php'; ?>
                <!-- Форма добавления нового комментария -->
                <form class="add-comment-form">
                    <input type="hidden" class="article-slug" value="<?= htmlspecialchars($article['slug']); ?>">
                    <input type="hidden" class="user-id" value="<?= htmlspecialchars($user['user_id']); ?>">
                    <div class="comment-input-wrapper">
                        <textarea id="markdown-comment-input" placeholder="Add a comment..." class="comment-input" maxlength="500"></textarea>
                        <div class="comment-editor-wrapper">
                            <span class="char-count">0/500</span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-add-comment">
                        <i class="fas fa-paper-plane"></i> <!-- Иконка для отправки -->
                    </button>
                </form>
            </div>
        </div>

    </article>
</main>

<footer>
    <?php include __DIR__ . '/../../views/base/profile_footer.php'; ?>
</footer>


<!-- Подключаем SimpleMDE JavaScript для рендеринга Markdown -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/showdown/1.9.1/showdown.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
<script src="/js/authorized_users/menu.js"></script>
<script src="/js/authorized_users/articles/add_markdown_comments.js"></script>
<script src="/js/authorized_users/articles/get_markdown.js"></script>

<script src="/js/authorized_users/articles/articles_reactions.js"></script>
<script src="/js/authorized_users/favourites/toogle_favourites.js"></script>
<script src="/js/authorized_users/articles/articles_comments.js"></script>
<script src="/js/authorized_users/articles/set_alert_to_delete_article.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.6.0/highlight.min.js"></script>
<script src="/js/authorized_users/articles/repost_article.js"></script>
<script src="/js/async_tasks/send_notifications.js"></script>


</body>
</html>
