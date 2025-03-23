function showList(type, slug, typeEntity = 'article') {
    fetch(`/${typeEntity}/${slug}/reactions`)
        .then(response => response.json())
        .then(data => {
            const modal = document.getElementById('modal');
            const modalTitle = document.getElementById('modal-title');
            const modalList = document.getElementById('modal-list');

            // Заголовок модального окна
            modalTitle.textContent = type === 'likes' ? 'List of Likes' : 'List of Dislikes';
            modalTitle.style.textAlign = 'center';
            modalTitle.style.margin = '0';

            // Формирование списка
            const users = data[type] || [];
            modalList.innerHTML = users.map(user => `
                <li class="user-item">
                    <img class="avatar" src="${user.user_avatar ? user.user_avatar : '/templates/images/profile.jpg'}" alt="${user.user_login}'s avatar">
                    <div class="user-info">
                        <a href="${user.link}" class="user-name">
                            <strong>${user.user_login}</strong>
                        </a>
                        <span class="user-email">${user.user_email}</span>
                    </div>
                </li>
            `).join('');

            modal.style.display = 'block';
        })
        .catch(error => {
            console.error('Error fetching reactions:', error);
        });
}

function closeModal() {
    document.getElementById('modal').style.display = 'none';
}