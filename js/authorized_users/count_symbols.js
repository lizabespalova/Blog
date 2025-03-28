document.addEventListener('DOMContentLoaded', function () {
    const textarea = document.getElementById('description');
    const charCount = document.getElementById('char-count');
    const maxLength = 1000;

    textarea.addEventListener('input', function () {
        if (textarea.value.length > maxLength) {
            textarea.value = textarea.value.substring(0, maxLength);
        }
        charCount.textContent = `${textarea.value.length}/${maxLength}`;
    });
});
