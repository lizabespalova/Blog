// Функция для создания графика лайков и дизлайков
function createLikesDislikesChart(likes, dislikes) {
    var ctx1 = document.getElementById('likesDislikesChart').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: ['Likes', 'Dislikes'],
            datasets: [{
                label: 'Likes and Dislikes',
                data: [likes, dislikes],
                backgroundColor: ['#4caf50', '#f44336'], // Зеленый для лайков, красный для дизлайков
                borderColor: ['#4caf50', '#f44336'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Функция для создания графика просмотров
function createViewsChart(views) {
    var ctx2 = document.getElementById('viewsChart').getContext('2d');
    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: ['Views'],
            datasets: [{
                label: 'Views',
                data: [views],
                backgroundColor: 'rgba(0, 123, 255, 0.2)',
                borderColor: '#007bff',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}
// Вызов функций с данными, полученными через DOM
document.addEventListener('DOMContentLoaded', function() {
    var likes = parseInt(document.getElementById('likes').textContent);
    var dislikes = parseInt(document.getElementById('dislikes').textContent);
    var views = parseInt(document.getElementById('views').textContent);

    createLikesDislikesChart(likes, dislikes);
    createViewsChart(views);
});
