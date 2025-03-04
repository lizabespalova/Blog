function scrollVideos(direction) {
    const container = document.querySelector('.video-scroll');
    const scrollAmount = 320; // Смещение при нажатии
    container.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
}