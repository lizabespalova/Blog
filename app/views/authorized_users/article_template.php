<?php
// Обработка тегов
$tagsOutput = '-'; // Значение по умолчанию

if (!empty($article['tags'])) {
    // Разбиваем строку на массив
    $tagsArray = explode(',', $article['tags']);
    // Преобразуем массив обратно в строку с использованием implode
    $tagsOutput = htmlspecialchars(implode(', ', $tagsArray));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?></title>
    <link rel="stylesheet" href="/css/profile/article_template.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Подключаем стили SimpleMDE для рендеринга Markdown -->
    <link rel="stylesheet" href="/css/profile/markdown.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.6.0/styles/default.min.css">
<!--    <base href="http://localhost:8080/">-->
</head>
<body>
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
                    <img src="<?php echo htmlspecialchars($author_info['user_avatar']); ?>" alt="Author Avatar" class="author-avatar">
                </a>
                <!-- Заголовок статьи -->
                <h2 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h2>
                <?php if ($_SESSION['user']['user_login'] === $article['author']) : ?>
                    <!-- Кнопки редактирования и удаления только для автора статьи -->
                    <div class="article-actions">
                        <a href="/articles/edit/<?php echo urlencode($article['slug']); ?>" class="btn btn-edit">Edit</a>
                        <a href="javascript:void(0);" class="btn btn-delete" onclick="confirmDelete(event, '<?php echo urlencode($article['slug']); ?>')">Delete</a>
                    </div>
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
                    <div id="rendered-content">
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
                    <button class="btn-like" title="Like" data-slug="<?php echo htmlspecialchars($article['slug']); ?>" data-user_id="<?php echo htmlspecialchars($user['user_id']); ?>">
                        <i class="fas fa-thumbs-up"></i>
                        <span class="like-count"><?php echo htmlspecialchars($article['likes']); ?></span> <!-- Здесь будет отображаться количество лайков -->
                    </button>

                    <!-- Дизлайки -->
                    <button class="btn-dislike" title="Dislike" data-slug="<?php echo htmlspecialchars($article['slug']); ?>" data-user_id="<?php echo htmlspecialchars($user['user_id']); ?>">
                        <i class="fas fa-thumbs-down"></i>
                        <span class="dislike-count"><?php echo htmlspecialchars($article['dislikes']); ?></span> <!-- Здесь будет отображаться количество дизлайков -->
                    </button>

                    <!-- Избранное -->
                    <button class="btn-favorite" title="Add to Favorites">
                        <i class="fas fa-star"></i>
                    </button>

                    <!-- Репост -->
                    <button class="btn-repost" title="Repost">
                        <i class="fas fa-share-alt"></i>
                    </button>
                </div>
            </section>

            <!-- Блок с комментариями -->
            <section class="comments-section">
                <h3>Comments</h3>
                <div class="comments-container">
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment">
                            <div class="comment-author">
                                <a href="<?= htmlspecialchars($comment['link']); ?>" class="comment-author-link">
                                    <img src="<?= htmlspecialchars($comment['user_avatar']); ?>" alt="Author Avatar" class="comment-author-avatar">
                                    <span class="comment-author-name"><?= htmlspecialchars($comment['user_login']); ?></span>
                                </a>
                            </div>
                            <div class="comment-content">
                                <p><?= htmlspecialchars($comment['comment_text']); ?></p>
                            </div>
                            <div class="comment-actions">
                                <span class="comment-date">Posted on: <?= htmlspecialchars($comment['created_at']); ?></span>
                                <div class="comment-buttons">
                                    <button class="btn-like">👍</button>
                                    <button class="btn-dislike">👎</button>
                                    <button class="btn-reply" data-comment-id="<?= $comment['id']; ?>">Reply</button>
                                    <button class="btn-toggle-replies">⮟</button> <!-- Кнопка для раскрытия ответов -->
                                </div>
                            </div>

                            <!-- Контейнер для вложенных ответов -->
                            <div class="replies-container" style="display: none;">
                                <?php if (!empty($comment['replies'])): ?>
                                    <?php foreach ($comment['replies'] as $reply): ?>
                                        <div class="comment reply">
                                            <div class="comment-author">
                                                <a href="<?= htmlspecialchars($reply['link']); ?>" class="comment-author-link">
                                                    <img src="<?= htmlspecialchars($reply['user_avatar']); ?>" alt="Author Avatar" class="comment-author-avatar">
                                                    <span class="comment-author-name"><?= htmlspecialchars($reply['user_login']); ?></span>
                                                </a>
                                            </div>
                                            <div class="comment-content">
                                                <p><?= htmlspecialchars($reply['comment_text']); ?></p>
                                            </div>
                                            <div class="comment-actions">
                                                <span class="comment-date">Posted on: <?= htmlspecialchars($reply['created_at']); ?></span>
                                                <div class="comment-buttons">
                                                    <button class="btn-like">👍</button>
                                                    <button class="btn-dislike">👎</button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>No replies yet.</p> <!-- Сообщение, если нет ответов -->
                                <?php endif; ?>
                            </div>

                            <!-- Форма ответа на комментарий -->
                            <form class="reply-comment-form" data-parent-id="<?= $comment['id']; ?>" style="display: none;">
                                <textarea placeholder="Add a reply..." class="reply-input"></textarea>
                                <button type="submit" class="btn btn-add-reply">Post Reply</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Форма добавления нового комментария -->
                <form class="add-comment-form">
                    <input type="hidden" class="article-slug" value="<?= htmlspecialchars($article['slug']); ?>">
                    <input type="hidden" class="user-id" value="<?= htmlspecialchars($user['user_id']); ?>">
                    <textarea placeholder="Add a comment..." class="comment-input"></textarea>
                    <button type="submit" class="btn btn-add-comment">Post Comment</button>
                </form>
            </section>




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
<script src="/js/authorized_users/get_markdown.js"></script>
<script src="/js/authorized_users/articles_reactions.js"></script>
<script src="/js/authorized_users/articles_comments.js"></script>
<script src="/js/authorized_users/set_alert_to_delete_article.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.6.0/highlight.min.js"></script>

</body>
</html>
