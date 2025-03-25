function openVisibilityModal() {
    document.getElementById('visibilityModal').style.display = 'block';
    document.getElementById('settings-menu').classList.remove('show'); // Скрываем меню
}

function closeVisibilityModal() {
    document.getElementById('visibilityModal').style.display = 'none';
}



// Переключение блока с выбором пользователей
document.querySelectorAll('input[name="visibility"]').forEach(el => {
    el.addEventListener('change', function () {
        document.getElementById('customUsersBlock').style.display = this.value === 'custom' ? 'block' : 'none';
    });
});

// Поиск пользователей по логину (AJAX)
function searchUsers(query) {
    if (query.length < 2) return;

    fetch(`/search-users?query=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(users => {
            const results = document.getElementById('userSearchResults');
            results.innerHTML = '';

            users.forEach(user => {
                const li = document.createElement('li');
                li.classList.add('user-item');
                li.style.cursor = 'pointer';
                li.onclick = () => addUserToSelected(user);

                const avatar = document.createElement('img');
                avatar.classList.add('avatar');
                avatar.src = user.user_avatar ? user.user_avatar : '/templates/images/profile.jpg';
                avatar.alt = `${user.user_login}'s avatar`;

                const userInfo = document.createElement('div');
                userInfo.classList.add('user-info');

                const nameLink = document.createElement('a');
                nameLink.href = user.link || '#';
                nameLink.classList.add('user-name');
                nameLink.innerHTML = `<strong>${user.user_login}</strong>`;

                userInfo.appendChild(nameLink);

                li.appendChild(avatar);
                li.appendChild(userInfo);

                results.appendChild(li);
            });
        });
}


const selectedUserIds = new Set();

function addUserToSelected(user) {
    if (selectedUserIds.has(user.user_id)) return;

    selectedUserIds.add(user.user_id);

    const li = document.createElement('li');
    li.textContent = user.user_login;
    li.setAttribute('data-id', user.user_id);

    const removeBtn = document.createElement('span');
    removeBtn.textContent = ' ×';
    removeBtn.style.color = 'red';
    removeBtn.style.cursor = 'pointer';
    removeBtn.onclick = () => {
        selectedUserIds.delete(user.user_id);
        li.remove();
    };

    li.appendChild(removeBtn);
    document.getElementById('selectedUsers').appendChild(li);
}


document.getElementById('visibilityForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const formData = new FormData(this);
    const selectedUsers = Array.from(selectedUserIds);
    formData.append('user_ids', JSON.stringify(selectedUsers));

    const courseId = document.getElementById('course-id').value;
    formData.append('course_id', courseId);

    const visibility = document.querySelector('input[name="visibility"]:checked').value;
    formData.append('visibility', visibility);

    // Первый запрос
    fetch('/save-visibility', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = "/success?message=" + 'Settings saved';
                closeVisibilityModal();

                // Второй запрос с использованием FormData
                const secondFormData = new FormData();
                secondFormData.append('course_id', courseId);
                secondFormData.append('status', visibility === 'public' ? 1 : 0); // Открыть для всех или закрыть

                fetch('/update-course-status', {
                    method: 'POST',
                    body: secondFormData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = "/success?message=" + 'Course status updated';

                        } else {
                            alert('Error updating course status: ' + data.error);
                        }
                    })
                    .catch(error => console.error('Error updating course status:', error));
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => console.error('Error saving visibility settings:', error));
});

