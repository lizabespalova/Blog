<div class="course-grid">
    <?php foreach ($courses as $course): ?>
        <div class="course-card">
            <img src="<?= htmlspecialchars('/' . ltrim($course['cover_image'], '/')) ?>" alt="Обложка курса">
            <h3><?= htmlspecialchars($course['title']) ?></h3>

            <?php
            // Условие для проверки доступности курса
            $isAccessible = ($course['visibility_type'] == 'public' || // Курс публичный
                $course['user_id'] == ($_SESSION['user']['user_id'] ?? null) || // Владелец курса
                ($course['visibility_type'] == 'subscribers' && !empty($course['isSubscriber']))); // Подписчик на курс
            ?>

            <!-- Отображаем кнопки и информацию о доступности курса -->
            <?php if ($isAccessible): ?>
                <a href="/course/<?= $course['course_id'] ?>" class="btn"><?= $translations['open_course']?></a>
            <?php else: ?>
                <div class="locked-course">
                    <span class="lock-icon">🔒</span>
                    <!-- Убираем текст "Private course", но если курс закрыт для подписчиков, показываем кнопку подписки -->
                    <?php if ($course['visibility_type'] === 'subscribers' && empty($course['isSubscriber'])): ?>
                        <!-- Добавляем ссылку на страницу владельца -->
                        <a href="/profile/<?= $course['owner'] ?>" class="btn"><?= $translations['follow_owner'] ?></a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php if ($course['hideEmail']==0 && $course['visibility_type'] === 'custom' && !$isAccessible): ?>
                <?php if (!empty($course['email'])): ?>
                    <a href="mailto:<?= htmlspecialchars($course['email']) ?>" class="btn btn-contact">
                        <?= $translations['request_access'] ?>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
            <!-- Отображаем рейтинг курса (всегда, независимо от доступности) -->
            <div class="course-rating">
                <?php
                // Получаем рейтинг курса (средний рейтинг)
                $rating = $course['rating']; // Средний рейтинг (должен быть в диапазоне от 0 до 5)

                if ($rating === null) {
                    $rating = 0; // если рейтинг не задан, ставим 0
                }

                // Ограничиваем рейтинг до 5
                $rating = min($rating, 5);

                // Считаем полные звезды
                $fullStars = floor($rating);
                // Проверяем на полузвезду
                $halfStar = ($rating - $fullStars) >= 0.5 ? true : false;
                // Пустые звезды
                $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0); // Перерасчет пустых звезд
                ?>

                <!-- Полные звезды -->
                <?php for ($i = 0; $i < $fullStars; $i++): ?>
                    <span class="star full">&#9733;</span>
                <?php endfor; ?>

                <!-- Полузвезда -->
                <?php if ($halfStar): ?>
                    <span class="star half">&#9733;</span>
                <?php endif; ?>

                <!-- Пустые звезды -->
                <?php for ($i = 0; $i < $emptyStars; $i++): ?>
                    <span class="star empty">&#9734;</span>
                <?php endfor; ?>
                <!-- Числовое значение рейтинга -->
                <span class="rating-number">(<?= number_format($rating, 1) ?>)</span>
            </div>
            <?php if (!empty($course['favourites'])): ?>
                <div class="course-stats">
                    <?= $translations['students_enrolled'] ?>: <?= htmlspecialchars($course['favourites']) ?></i>
                </div>
            <?php endif; ?>

        </div>
    <?php endforeach; ?>
</div>
