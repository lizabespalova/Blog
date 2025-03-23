function openVisibilityModal() {
    document.getElementById('visibilityModal').style.display = 'block';
    document.getElementById('settings-menu').style.display = 'none';
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
                li.textContent = user.user_login + ' (' + user.user_email + ')';
                li.style.cursor = 'pointer';
                li.onclick = () => addUserToSelected(user);
                results.appendChild(li);
            });
        });
}

const selectedUserIds = new Set();

function addUserToSelected(user) {
    if (selectedUserIds.has(user.user_id)) return;

    selectedUserIds.add(user.user_id);

    const li = document.createElement('li');
    li.textContent = user.user_login + ' (' + user.user_email + ')';
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