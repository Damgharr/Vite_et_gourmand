const section = document.getElementById('reviews');
const track = section.querySelector('& > div > div');
const leftBtn = section.querySelector('.navigate-left');
const rightBtn = section.querySelector('.navigate-right');
const cards = section.querySelectorAll('& > div > div > div');

let currentIndex = 0;
let itemsPerView = window.innerWidth > 480 ? 3 : 1;
const maxIndex = cards.length - itemsPerView;

function updateCarousel() {
    itemsPerView = window.innerWidth > 480 ? 3 : 1;
    if (currentIndex > maxIndex) {
        currentIndex = maxIndex;
    }
    const translatePercent = currentIndex * (100 / itemsPerView);
    track.style.transform = 'translateX(-' + translatePercent + '%)';
    leftBtn.style.opacity = currentIndex === 0 ? '0.3' : '1';
    rightBtn.style.opacity = currentIndex >= maxIndex ? '0.3' : '1';

}


leftBtn.addEventListener('click', function () {
    if (currentIndex > 0) {
        currentIndex--;
        updateCarousel();
    }
});



rightBtn.addEventListener('click', function () {
    if (currentIndex < maxIndex) {
        currentIndex++;
        updateCarousel();
    }
});

