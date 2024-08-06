const body = document.querySelector('#dynamicElementsContainer');

const elements = {
    item1: {
        width: '350px',
        height: '300px',
        backgroundImage: 'url("templates/images/screensaver.png")',
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        title: 'Java',
        titleColor: '#ffffff',
        titleFontSize: '20px',
        titleBackground: 'rgba(0, 0, 0, 0.5)',
        titlePadding: '10px',
        titleTextAlign: 'center',
        link: 'pages/java.php',
        lessons: 10
    },
    item2: {
        width: '350px',
        height: '300px',
        backgroundImage: 'url("templates/images/screensaver.png")',
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        title: 'Spring',
        titleColor: '#ffffff',
        titleFontSize: '20px',
        titleBackground: 'rgba(0, 0, 0, 0.5)',
        titlePadding: '10px',
        titleTextAlign: 'center',
        link: 'pages/spring.php',
        lessons: 10
    },
};

for (const key in elements) {
    if (elements.hasOwnProperty(key)) {
        const item = elements[key];

        const container = document.createElement('div');
        container.style.width = item.width;
        container.style.height = item.height;
        container.style.display = 'flex';
        container.style.flexDirection = 'column';
        container.style.justifyContent = 'flex-end';
        container.style.padding = '10px';
        container.style.backgroundColor = item.titleBackground;
        container.style.transition = 'transform 0.3s, box-shadow 0.3s';
        container.style.position = 'relative';

        // Создаем заголовок
        const title = document.createElement('div');
        title.textContent = item.title;
        title.style.color = item.titleColor;
        title.style.fontSize = item.titleFontSize;
        title.style.textAlign = item.titleTextAlign;
        title.style.padding = item.titlePadding;
        title.style.marginBottom = '10px';

        // Добавляем заголовок в общий контейнер
        container.appendChild(title);

        // Добавляем фоновое изображение карточки
        const card = document.createElement('div');
        card.style.width = '100%';
        card.style.height = '100%';
        card.style.backgroundImage = item.backgroundImage;
        card.style.backgroundSize = item.backgroundSize;
        card.style.backgroundPosition = item.backgroundPosition;
        card.style.marginBottom = '10px';
        container.appendChild(card);

        const infoContainer = document.createElement('div');
        infoContainer.style.width = '100%';
        infoContainer.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
        infoContainer.style.color = '#ffffff';
        infoContainer.style.padding = '5px 10px';
        infoContainer.style.boxSizing = 'border-box';
        infoContainer.style.textAlign = 'center';
        infoContainer.style.display = 'flex';
        infoContainer.style.justifyContent = 'space-between';

        // Добавляем информацию о количестве уроков
        const lessonsInfo = document.createElement('span');
        lessonsInfo.textContent = `Lessons: ${item.lessons}`;
        infoContainer.appendChild(lessonsInfo);

        // Добавляем информацию о количестве заходов на страницу (в виде глаза)
        const viewsInfo = document.createElement('span');
        viewsInfo.textContent = `Views: Loading...`;

        // Функция для получения количества просмотров по ссылке
        function fetchViewsCount(link, viewsElement) {
            fetch(`/api/views.php?link=${encodeURIComponent(link)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        viewsElement.textContent = `Views: N/A`;
                        console.error('Error fetching views count:', data.error);
                    } else {
                        viewsElement.textContent = `Views: ${data.views} 👁️`;
                        // Сохраняем количество просмотров для дальнейшего использования
                        item.views = data.views;
                    }
                })
                .catch(error => {
                    console.error('Error fetching views count:', error);
                    viewsElement.textContent = `Views: N/A`;
                });
        }

        // Загружаем количество просмотров при первой загрузке
        fetchViewsCount(item.link, viewsInfo);
        infoContainer.appendChild(viewsInfo);

        // Добавляем обработчик клика на карточку
        container.addEventListener('click', function(event) {
            event.preventDefault(); // Останавливаем действие по умолчанию

            console.log(`Fetching views for link: ${item.link}`);

            // Увеличиваем счетчик просмотров
            fetch(`/api/update_views.php?link=${encodeURIComponent(item.link)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        // Обновляем количество просмотров на UI
                        item.views++;
                        viewsInfo.textContent = `Views: ${item.views} 👁️`;
                    } else {
                        console.error('Error updating views count:', data.error);
                    }
                    window.location.href = item.link; // Переход на указанную страницу при клике
                })
                .catch(error => {
                    console.error('Error updating views count:', error);
                    window.location.href = item.link; // Все равно перенаправляем в случае ошибки
                });
        });

        // Добавляем контейнер с информацией в основной контейнер
        container.appendChild(infoContainer);

        // Добавляем стили для hover эффекта (тень при поднятии)
        container.addEventListener('mouseenter', function() {
            container.style.transform = 'translateY(-5px)';
            container.style.boxShadow = '0px 5px 15px rgba(0,0,0,0.3)';
        });

        container.addEventListener('mouseleave', function() {
            container.style.transform = 'translateY(0)';
            container.style.boxShadow = 'none';
        });

        // Добавляем контейнер в тело документа
        body.appendChild(container);
    }
}
