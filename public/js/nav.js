
console.log("script executée");
console.log(window.location.search);

function setNavMenuLogic() {

    const nav = document.querySelector("nav");
    const button = nav.querySelector("button");
    const ul = nav.querySelector("ul");

    button.addEventListener("click", function () {
        console.log("bouton cliqué !");
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
}

document.addEventListener("DOMContentLoaded", setNavMenuLogic());

