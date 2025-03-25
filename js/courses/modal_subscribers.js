function closeSubscriberModal() {
    document.getElementById('subscribersModal').style.display = 'none';
}
// Открытие модального окна с подписчиками
function openSubscribersModal(courseId) {
    document.getElementById('subscribersModal').style.display = 'block';

    fetch(`/courses/get-subscribers/${courseId}`)
        .then(response => response.json())
        .then(data => {
            const subscribersList = document.getElementById('subscribersList');
            subscribersList.innerHTML = '';

            if (data && Array.isArray(data.subscribers) && data.subscribers.length > 0) {
                data.subscribers.forEach(subscriber => {
                    const li = document.createElement('li');
                    li.classList.add('user-item');
                    li.setAttribute('data-id', subscriber.user_id);

                    const avatar = document.createElement('img');
                    avatar.classList.add('avatar');
                    avatar.src = subscriber.user_avatar ? subscriber.user_avatar : '/templates/images/profile.jpg';
                    avatar.alt = subscriber.user_login;

                    const userInfo = document.createElement('div');
                    userInfo.classList.add('user-info');

                    const nameLink = document.createElement('a');
                    nameLink.href = subscriber.profile_link || '#';
                    nameLink.classList.add('user-name');
                    nameLink.innerHTML = `<strong>${subscriber.user_login}</strong>`;

                    userInfo.appendChild(nameLink);

                    const removeBtn = document.createElement('span');
                    removeBtn.classList.add('remove-subscriber');
                    removeBtn.textContent = ' ×';
                    removeBtn.style.color = 'red';
                    removeBtn.style.cursor = 'pointer';
                    removeBtn.onclick = () => removeSubscriber(subscriber.user_id, courseId, li);

                    li.appendChild(avatar);
                    li.appendChild(userInfo);
                    li.appendChild(removeBtn);
                    subscribersList.appendChild(li);
                });
            } else {
                subscribersList.innerHTML = '<li>No subscribers found</li>';
            }
        })
        .catch(error => {
            console.error('Error fetching subscribers:', error);
        });
}

function removeSubscriber(userId, courseId, listItem) {
    if (!confirm('Are you sure you want to remove this subscriber?')) return;

    fetch(`/courses/remove-subscriber`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ user_id: userId, course_id: courseId })
    })
        .then(response => {
            if (!response.ok) { // Если статус не ок (например, 500 или 404)
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                listItem.remove();
                window.location.href = "/success?message=" + encodeURIComponent(data.message);
            } else {
                alert('Error removing subscriber: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error removing subscriber:', error); // Логирует ошибку сети или JSON
        });
}
