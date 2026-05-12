const nav = document.querySelector("nav");
const toggle = nav.querySelector("button");
const ul = nav.querySelector("ul");

toggle.addEventListener("click", function () {
    const isOpen = ul.getAttribute("data-open") === "true";
    if (isOpen === false) {
        ul.classList.add("open");
    }
});

nav.addEventListener("click", function (e) {
    if (e.target === e.currentTarget) {
        if (ul.classList.contains("open") && !ul.contains(e.target)) {
            ul.classList.remove("open");
        }
    }
});
