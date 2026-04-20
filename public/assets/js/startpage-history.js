document.addEventListener("DOMContentLoaded", function() {
    const sidebarLinks = document.querySelectorAll("#sidebar .nav-link");
    sidebarLinks.forEach(link => {
        if (link.getAttribute("href") === "/start/history") {
            link.classList.remove("text-white-50");
            link.classList.add("active");
        }
    });
});
