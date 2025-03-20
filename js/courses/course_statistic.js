document.addEventListener('DOMContentLoaded', function () {
    var articles = window.popularArticles || [];

    var titles = [];
    var likes = [];

    for (var i = 0; i < articles.length; i++) {
        var article = articles[i];
        titles.push(article.title);
        likes.push(parseInt(article.likes || 0, 10));
    }

    var ctx = document.getElementById('likesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',  // Меняем тип с 'bar' на 'line'
        data: {
            labels: titles,
            datasets: [{
                label: 'Количество лайков',
                data: likes,
                backgroundColor: 'rgba(76, 175, 80, 0.7)',  // Цвет фона для линии
                borderColor: 'rgba(76, 175, 80, 1)',        // Цвет линии
                borderWidth: 2,                             // Толщина линии
                fill: false                                  // Не закрашивать область под линией
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Лайки по статьям'
                },
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                },
                x: {
                    ticks: {
                        autoSkip: false,
                        maxRotation: 45,
                        minRotation: 30
                    }
                }
            }
        }
    });
});
