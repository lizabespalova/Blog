document.addEventListener('DOMContentLoaded', function() {
    const hoverImage = document.querySelector('.hover-image');
    if (hoverImage) {
        hoverImage.addEventListener('mousemove', function(e) {
            const x = e.clientX / window.innerWidth;
            const y = e.clientY / window.innerHeight;

            hoverImage.style.transform = `translate(-${x * 50}px, -${y * 50}px)`;
        });
    }
});
