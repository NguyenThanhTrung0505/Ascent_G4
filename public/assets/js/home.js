window.addEventListener("scroll", function () {
    const headerBottom = document.querySelector(".header-bottom");

    if (window.scrollY > 60) {
        headerBottom.classList.add("fixed");
    } else {
        headerBottom.classList.remove("fixed");
    }
});
