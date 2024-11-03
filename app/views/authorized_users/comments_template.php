<div class="comments-container">
    <?php
    // Создаем экземпляр Parsedown
    $parsedown = new Parsedown();

    // Вывод комментариев
    foreach ($comments as $index => $comment):
        if ($comment['parent_id'] === null): // Основной комментарий
            ?>
            <div class="comment" style="display: <?= $index <= 3 ? 'block' : 'none'; ?>">
                <div class="comment-author">
                    <a href="<?= htmlspecialchars($comment['link']); ?>" class="comment-author-link">
                        <img src="<?= htmlspecialchars($comment['user_avatar']); ?>" alt="Author Avatar" class="comment-author-avatar">
                        <span class="comment-author-name"><?= htmlspecialchars($comment['user_login']); ?></span>
                    </a>
                </div>
                <div id="rendered-content-2" class="comment-content">
                    <?php
                    // Получаем текст комментария
                    $comment_text = $comment['comment_text'];
                    // Обработка формул в формате $$...$$
                    $comment_text = preg_replace('/\$\$([\s\S]*?)\$\$/', '<div class="formula-container">$1</div>', $comment_text);
                    // Преобразуем Markdown в HTML и выводим
                    echo $parsedown->text($comment_text);
                    ?>
                </div>


                <div class="comment-actions">
                    <span class="comment-date">Posted on: <?= htmlspecialchars($comment['created_at']); ?></span>
                    <div class="comment-buttons">
                        <button class="btn-like">👍</button>
                        <button class="btn-dislike">👎</button>
                        <button class="btn-reply" data-comment-id="<?= $comment['id']; ?>"><i class="fas fa-reply"></i></button>
                        <button class="btn-toggle-replies">⮟</button> <!-- Кнопка для раскрытия ответов -->
                    </div>
                </div>

                <!-- Контейнер для вложенных ответов -->
                <div class="replies-container" style="display: none;">
                    <?php
                    $replyCount = 0; // Инициализация счетчика ответов
                    foreach ($comments as $reply):
                        if ($reply['parent_id'] == $comment['id']):
                            $replyCount++; // Увеличиваем счетчик
                            ?>
                            <div class="comment reply" style="display: <?= $replyCount <= 3 ? 'block' : 'none'; ?>">
                                <div class="comment-author">
                                    <a href="<?= htmlspecialchars($reply['link']); ?>" class="comment-author-link">
                                        <img src="<?= htmlspecialchars($reply['user_avatar']); ?>" alt="Author Avatar" class="comment-author-avatar">
                                        <span class="comment-author-name"><?= htmlspecialchars($reply['user_login']); ?></span>
                                    </a>
                                </div>
                                <div class="comment-content" >
                                    <?= $parsedown->text($reply['comment_text']); ?> <!-- Преобразуем Markdown в HTML -->
                                </div>
                                <div class="comment-actions">
                                    <span class="comment-date">Posted on: <?= htmlspecialchars($reply['created_at']); ?></span>
                                    <div class="comment-buttons">
                                        <button class="btn-like">👍</button>
                                        <button class="btn-dislike">👎</button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; endforeach; ?>
                    <!-- Кнопка для показа остальных ответов -->
                    <button class="btn-show-more-replies">Show more replies</button>
                </div>

                <!-- Форма ответа на комментарий -->
                <form class="reply-comment-form" data-parent-id="<?= $comment['id']; ?>" style="display: none;">
                    <textarea placeholder="Add a reply..." class="reply-input"></textarea>
                    <button type="submit" class="btn btn-add-reply">Post Reply</button>
                </form>
            </div>
        <?php endif; endforeach; ?>
</div>
