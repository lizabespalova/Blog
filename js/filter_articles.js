// Универсальная функция для фильтрации и выделения текста
function filterSearch(inputSelector, cardSelectors, textSelectors) {
    const query = document.querySelector(inputSelector).value.toLowerCase().trim();
    const cards = document.querySelectorAll(cardSelectors);

    if (!query) {
        cards.forEach(card => {
            if (card.dataset.display) {
                card.style.display = card.dataset.display;
            } else {
                card.style.display = '';
            }
            card.querySelectorAll('.highlight').forEach(el => {
                el.outerHTML = el.innerText;
            });
        });
        return;
    }

    cards.forEach(card => {
        const searchableText = Array.from(card.querySelectorAll(textSelectors))
            .map(field => field.textContent.toLowerCase())
            .join(' ');

        if (searchableText.includes(query)) {
            if (card.dataset.display) {
                card.style.display = card.dataset.display;
            } else {
                card.style.display = '';
            }
            highlightText(card, query);
        } else {
            card.style.display = 'none';
        }
    });
}

// Функция для выделения найденного текста
function highlightText(card, query) {
    card.querySelectorAll('.highlight').forEach(el => {
        el.outerHTML = el.innerText;
    });

    const regex = new RegExp(`(${query})`, 'gi');
    const textElements = card.querySelectorAll('h3, p');

    textElements.forEach(el => {
        el.innerHTML = el.textContent.replace(regex, '<span class="highlight">$1</span>');
    });
}
function performSearch() {
    const query = searchInput.value.trim();
    const type = getSearchType(); // Получаем тип поиска (articles, courses, writers)
    const userId = sessionStorage.getItem('userId') || null;

    const resultsContainer = document.getElementById('content-container');

    // Очищаем контейнер перед рендерингом
    resultsContainer.innerHTML = "";

    fetch(`/search-item?query=${encodeURIComponent(query)}&type=${type}`)
        .then(response => response.json())
        .then(data => {
            // Проверяем, какие данные нужно рендерить
            if (type === 'articles' && (!data.articles || data.articles.length === 0)) {
                resultsContainer.innerHTML = "<p>No articles found</p>";
                return;
            }

            if (type === 'courses' && (!data.courses || data.courses.length === 0)) {
                resultsContainer.innerHTML = "<p>No courses found</p>";
                return;
            }

            if (type === 'writers' && (!data.writers || data.writers.length === 0)) {
                resultsContainer.innerHTML = "<p>No writers found</p>";
                return;
            }

            // Отдельно рендерим статьи, если тип 'articles' или 'all'
            if (type === 'articles' || type === 'all') {
                if (data.articles && data.articles.length > 0) {
                    data.articles.forEach(article => {
                        resultsContainer.innerHTML += `
                            <div class="article-feed">
                               <img src="${article.user_avatar || '/templates/images/profile.jpg'}" 
                                 alt="${article.user_login}" class="article-avatar">
                                <div class="article-info">
                                    <h2>Author: <a href="/profile/${article.user_login}">${article.user_login}</a></h2>
                                    <p><a href="/articles/${article.slug}">${article.title}</a></p>
                                    <div class="article-content" id="rendered-content-${article.id}">
                                        ${article.parsed_content ? truncateContent(article.parsed_content) : 'No content available'}
                                    </div>
                                    ${article.parsed_content && article.parsed_content.length > 300 ?
                            `<a href="/articles/${article.slug}" class="read-more">Read more</a>` : ''}
                                    <p><small>Date: ${new Date(article.created_at).toLocaleDateString()}</small></p>
                                </div>
                            </div>
                        `;
                    });
                }
            }

            // Отдельно рендерим курсы, если тип 'courses' или 'all'
            if (type === 'courses' || type === 'all') {
                if (data.courses && data.courses.length > 0) {
                    const gridContainer = document.createElement('div');
                    gridContainer.classList.add('course-grid');

                    data.courses.forEach(course => {
                        let isAccessible = (course.visibility_type === 'public' ||
                            course.user_id === userId ||
                            (course.visibility_type === 'subscribers' && course.isSubscriber));

                        const coverImage = course.cover_image
                            ? `/${course.cover_image.replace(/^\/+/, '')}`
                            : '/templates/images/default-course.jpg';

                        const courseCard = document.createElement('div');
                        courseCard.classList.add('course-card');

                        courseCard.innerHTML = `
                            <img src="${coverImage}" alt="Course cover">
                            <h3>${course.title}</h3>
                            <p>${course.description || 'No description available'}</p>
                            ${isAccessible
                            ? `<a href="/course/${course.course_id}" class="btn">Open Course</a>`
                            : `<div class="locked-course">
                                    <span class="lock-icon">🔒</span>
                                    <p>This course is private. 
                                        ${!course.hideEmail && course.email
                                ? `Contact: ${course.email}`
                                : 'Contact information hidden'}
                                    </p>
                                    ${!course.hideEmail && course.email
                                ? `<a href="mailto:${course.email}" class="btn btn-contact">Request Access</a>`
                                : ''}
                                   </div>`}
                        `;

                        gridContainer.appendChild(courseCard);
                    });

                    resultsContainer.appendChild(gridContainer);
                }
            }

            // Отдельно рендерим писателей, если тип 'writers' или 'all'
            if (type === 'writers' || type === 'all') {
                if (data.writers && data.writers.length > 0) {
                    data.writers.forEach(writer => {
                        resultsContainer.innerHTML += `
                            <div class="writers-grid" id="writers-grid">
                                <div class="writer-card">
                                    <img class="writer-avatar" 
                                         src="${writer.user_avatar || '/templates/images/profile.jpg'}" 
                                         alt="${writer.user_login}">
                                    <div class="writer-info">
                                        <h3>${writer.user_login}</h3>
                                        <p>${writer.user_specialisation || 'Specialisation not provided'}</p>
                                        <p class="stats">
                                            Followers: ${writer.followers_count || 0}
                                        </p>
                                        <a class="profile-link" href="/profile/${encodeURIComponent(writer.user_login)}">
                                            View Profile
                                        </a>
                                    </div>
                                </div>
                             </div>
                        `;
                    });
                }
            }
        })
        .catch(error => console.error("Error searching:", error));
}


function getSearchType() {
    const urlParams = new URLSearchParams(window.location.search);
    const section = urlParams.get('section'); // Получаем параметр section из URL
    switch (section) {
        case 'popular-articles':
            return 'articles';  // Если в URL указаны популярные статьи
        case 'popular-writers':
            return 'writers';   // Если в URL указаны популярные писатели
        case 'popular-courses':
            return 'courses';   // Если в URL указаны популярные курсы
        case 'feed':
            return 'feed';   // Если в URL указаны популярные курсы
        default:
            return 'all';       // По умолчанию ищем во всех категориях
    }
}


// Подключаем обработчики событий
const searchButton = document.getElementById('searchButton');
const searchInput = document.querySelector('.searching-input');

if (searchButton && searchInput) {
    searchButton.addEventListener('click', () => {
        performSearch();
    });
    searchInput.addEventListener('input', () => {
        filterSearch('.searching-input', '.card, .writer-card, .course-card', '.card-title, .card-meta, h3, p');
    });
} else {
    console.warn('Search button or input not found.');
}
function truncateContent(content, limit = 300) {
    if (content.length <= limit) {
        return content;
    }

    let truncated = content.substring(0, limit);
    let lastSpace = truncated.lastIndexOf(' ');

    if (lastSpace !== -1) {
        truncated = truncated.substring(0, lastSpace);
    }

    return truncated + '...';
}
