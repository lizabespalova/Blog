<div class="comments-container">
    <?php
    $totalComments = count($comments);
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
                <div class="comment-content">
                    <p><?= htmlspecialchars($comment['comment_text']); ?></p>
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
                    foreach ($comments as $reply):
                        if ($reply['parent_id'] == $comment['id']):
                            ?>
                            <div class="comment reply" style="display: <?= $replyCount <= 3 ? 'block' : 'none'; ?>">
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
