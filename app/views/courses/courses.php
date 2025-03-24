<div class="courses-list">
    <?php foreach ($filteredCourses as $course): ?>
        <div class="course-card">
            <img src="<?= htmlspecialchars('/' . ltrim($course['cover_image'], '/')) ?>" alt="Обложка курса">
            <h3><?= htmlspecialchars($course['title']) ?></h3>
            <p><?= htmlspecialchars($course['description']) ?></p>
            <a href="/course/<?= $course['course_id'] ?>" class="btn"><?= $translations['open_course']?></a>
        </div>
    <?php endforeach; ?>
</div>