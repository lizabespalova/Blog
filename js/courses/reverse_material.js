document.querySelectorAll('.toggle-materials-btn').forEach(button => {
    button.addEventListener('click', () => {
        const materialsList = button.closest('.material-group').querySelector('.materials-list');
        materialsList.classList.toggle('hidden');
        button.textContent = materialsList.classList.contains('hidden')
            ? '📂 Show materials ⬇'
            : '📂 Hide materials ⬆';
    });

});