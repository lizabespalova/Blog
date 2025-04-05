document.addEventListener("DOMContentLoaded", function () {
    const cookieNotice = document.getElementById("cookie-notice");
    const acceptButton = document.getElementById("accept-cookies");

    // Проверка, приняли ли куки
    if (localStorage.getItem("cookiesAccepted") === "true") {
        cookieNotice.style.display = "none";
    }

    // Нажатие "Понятно"
    acceptButton.addEventListener("click", function () {
        localStorage.setItem("cookiesAccepted", "true");
        cookieNotice.style.display = "none";
    });

    // Переход на политику конфиденциальности — можно оставить поведение по умолчанию
    const privacyLink = document.querySelector('#cookie-notice a[href="/privacy-policy"]');

    if (privacyLink) {
        privacyLink.addEventListener('click', function (e) {
            e.preventDefault();
            fetch('/privacy-policy')
                .then(response => {
                    if (response.ok) {
                        window.location.href = '/privacy-policy';
                    } else {
                        alert('Страница политики конфиденциальности недоступна.');
                    }
                })
                .catch(() => {
                    alert('Ошибка сети при попытке открыть политику конфиденциальности.');
                });
        });
    }});