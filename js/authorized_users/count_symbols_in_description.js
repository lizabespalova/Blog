const descriptionInput = document.getElementById('description');
const charCount = document.getElementById('char-count');

descriptionInput.addEventListener('input', () => {
    charCount.textContent = `${descriptionInput.value.length}/500`;
});
