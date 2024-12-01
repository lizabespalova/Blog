document.querySelectorAll('.comment-input-wrapper, .repost-buttons').forEach(wrapper => {
    const charCountForm = wrapper.querySelector('.char-count-form');
    const charCount = wrapper.querySelector('.char-count');

    if (charCountForm && charCount) {
            // Обновляем счетчик символов для конкретной области
            charCount.textContent = `${textarea.value.length}/500`;
            charCountForm.textContent = `${textarea.value.length}/500`;

    }
});
