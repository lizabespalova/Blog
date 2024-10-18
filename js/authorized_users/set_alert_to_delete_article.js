function confirmDelete(event, articleSlug) {
    event.preventDefault(); // Останавливаем обычный переход по ссылке
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545', // Красный цвет для подтверждения
        cancelButtonColor: '#6c757d',  // Серый цвет для отмены
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Если подтверждено, перенаправляем на путь удаления статьи
            window.location.href = '/articles/delete/' + articleSlug;
        }
    });
}
