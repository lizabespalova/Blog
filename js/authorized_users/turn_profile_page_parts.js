document.addEventListener("DOMContentLoaded", function () {
    const navItems = document.querySelectorAll(".navigation-item");
    const contentPages = document.querySelectorAll(".content-page");
    const menuIndicator = document.querySelector(".menu-indicator");
    const contentText = document.querySelector(".content-text"); // Блок описания

    // Initial indicator position (check if there's an active element)
    const activeItem = document.querySelector(".navigation-item.active");
    if (activeItem) {
        setIndicatorPosition(activeItem);
    }

    navItems.forEach((item) => {
        item.addEventListener("click", (e) => {
            e.preventDefault();

            // Update active class
            const currentActive = document.querySelector(".navigation-item.active");
            if (currentActive) {
                currentActive.classList.remove("active");
            }
            item.classList.add("active");

            // Update content
            const page = item.getAttribute("data-page");
            updateContent(page);

            // Move the indicator
            setIndicatorPosition(item);
        });
    });

    // Function to update content
    function updateContent(page) {
        contentPages.forEach((content) => {
            content.classList.add("hidden");
        });

        const targetContent = document.getElementById(`${page}-content`);
        if (targetContent) {
            targetContent.classList.remove("hidden");
        }

        // Управляем видимостью блока content-text
        if (page === "profile") {
            contentText.classList.remove("hidden");
        } else {
            contentText.classList.add("hidden");
        }
    }

    // Function to move the menu indicator
    function setIndicatorPosition(activeItem) {
        const left = activeItem.offsetLeft;
        const width = activeItem.offsetWidth;

        menuIndicator.style.transform = `translateX(${left}px)`;
        menuIndicator.style.width = `${width}px`;
    }
});
