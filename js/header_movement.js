let lastScrollTop = 0;
window.addEventListener("scroll", function(){
    let currentScroll = window.pageYOffset || document.documentElement.scrollTop;
    if (currentScroll > lastScrollTop){
        // Прокрутка вниз
        document.querySelector('.navbar').classList.remove('show');
    } else {
        // Прокрутка вверх
        document.querySelector('.navbar').classList.add('show');
    }
    lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
}, false);
