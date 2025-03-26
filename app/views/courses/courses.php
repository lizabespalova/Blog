<div class="course-grid">
<?php foreach ($courses as $course): ?>
    <div class="course-card">
        <img src="<?= htmlspecialchars('/' . ltrim($course['cover_image'], '/')) ?>" alt="Обложка курса">
        <h3><?= htmlspecialchars($course['title']) ?></h3>
        <p><?= htmlspecialchars($course['description']) ?></p>

        <?php
        // Условие для проверки доступности курса
        $isAccessible = ($course['visibility_type'] == 'public' || // Курс публичный
            $course['user_id'] == ($_SESSION['user']['user_id'] ?? null) || // Владелец курса
            ($course['visibility_type'] == 'subscribers' && !empty($course['isSubscriber']))); // Подписчик на курс
        ?>

        <?php if ($isAccessible): ?>
            <!-- Курс открыт для доступа -->
            <a href="/course/<?= $course['course_id'] ?>" class="btn"><?= $translations['open_course']?></a>
        <?php else: ?>
            <!-- Курс закрыт для доступа -->
            <div class="locked-course">
                <span class="lock-icon">🔒</span>
                <p><?= $translations['private_course'] ?>
                    <?php if (!empty($course['hideEmail']) && !$course['hideEmail']): ?>
                        (<?= !empty($course['email']) ? htmlspecialchars($course['email']) : $translations['contact_hidden'] ?>)
                    <?php else: ?>
                        (<?= $translations['contact_hidden'] ?>)
                    <?php endif; ?>
                </p>

                <?php if (!empty($course['hideEmail']) && !$course['hideEmail'] && !empty($course['email'])): ?>
                    <a href="mailto:<?= htmlspecialchars($course['email']) ?>" class="btn btn-contact">
                        <?= $translations['request_access'] ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
</div>