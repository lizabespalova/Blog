document.querySelectorAll('.progress-checkbox').forEach(el => {
    el.addEventListener('change', function () {
        const course_id = this.dataset.courseId; // Получаем course_id из data-атрибута
        const article_id = this.dataset.id; // Получаем id статьи
        const is_completed = this.checked ? 1 : 0;
        const video_link = this.dataset.videoLink || ''; // Если видео-линк есть, то берём его, иначе пустая строка
        fetch('/progress/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                course_id: course_id,
                article_id: article_id,
                video_link: video_link, // Добавляем видео-линк
                is_completed: is_completed
            })
        })
            .then(res => res.json())
            .then(data => {
                console.log(data.message);
            });
    });
});
