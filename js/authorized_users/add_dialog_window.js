document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modal');
    const closeButton = document.querySelector('.close');
    const modalText = document.getElementById('modal-text');

    function showModal(fullText) {
        console.log('Showing modal with text:', fullText); // Отладочный вывод
        modalText.innerText = fullText;
        modal.style.display = 'block'; // Убедитесь, что модальное окно становится видимым
    }

    const specialisationDisplay = document.getElementById('specialisation-display');
    const fullSpecialisationText = specialisationDisplay.getAttribute('data-full-text') || '';

    if (fullSpecialisationText.length > 30) {
        const shortText = fullSpecialisationText.substring(0, 30) + '...';
        specialisationDisplay.innerText = shortText;
        specialisationDisplay.style.cursor = 'pointer';
        specialisationDisplay.addEventListener('click', function() {
            showModal(fullSpecialisationText);
        });
    }

    const companyDisplay = document.getElementById('company-display');
    const fullCompanyText = companyDisplay.getAttribute('data-full-text') || '';

    if (fullCompanyText.length > 30) {
        const shortText = fullCompanyText.substring(0, 30) + '...';
        companyDisplay.innerText = shortText;
        companyDisplay.style.cursor = 'pointer';
        companyDisplay.addEventListener('click', function() {
            showModal(fullCompanyText);
        });
    }

    closeButton.addEventListener('click', function() {
        console.log('Closing modal'); // Отладочный вывод
        modal.style.display = 'none'; // Скрыть модальное окно
    });

    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            console.log('Clicked outside modal'); // Отладочный вывод
            modal.style.display = 'none'; // Скрыть модальное окно при клике вне области
        }
    });
});
