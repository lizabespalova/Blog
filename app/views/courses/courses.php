
<div class="courses-list">
    <?php foreach ($courses as $course): ?>
        <div class="course-card">
            <img src="<?= htmlspecialchars('/' . ltrim($course['cover_image'], '/')) ?>" alt="Обложка курса">
            <h3><?= htmlspecialchars($course['title']) ?></h3>
            <p><?= htmlspecialchars($course['description']) ?></p>

            <?php if (in_array($course, $filteredCourses)): ?>
                <!-- Курс открыт или пользователь — владелец -->
                <a href="/course/<?= $course['course_id'] ?>" class="btn"><?= $translations['open_course']?></a>
            <?php else: ?>
                <!-- Курс закрыт -->
                <div class="locked-course">
                    <span class="lock-icon">🔒</span>
                    <p><?= $translations['private_course'] ?>
                        <?php if (!$hideEmail): ?>
                            (<?= htmlspecialchars($email) ?>)
                        <?php else: ?>
                            (<?= $translations['contact_hidden'] ?>)
                        <?php endif; ?>
                    </p>

                    <?php if (!$hideEmail): ?>
                        <a href="mailto:<?= htmlspecialchars($email) ?>" class="btn btn-contact">
                            <?= $translations['request_access'] ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    <?php endforeach; ?>
</div>
