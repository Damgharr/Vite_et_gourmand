const section = document.getElementById('reviews');
const track = section.querySelector('section > div > div');
const leftBtn = section.querySelector('button:first-of-type');
const rightBtn = section.querySelector('button:last-of-type');
const cards = section.querySelectorAll('section > div > div > div');

let currentIndex = 0;

function isDesktop() {
    return window.innerWidth > 480;
}

function getItemsPerView() {
    return isDesktop() ? 3 : 1;
}

function getMaxIndex() {
    return Math.max(0, cards.length - getItemsPerView());
}

console.log(cards);

function updateCarousel() {
    console.log(currentIndex);

    if (!isDesktop()) {
        track.style.transform = '';
        return;
    }

    const itemsPerView = getItemsPerView();
    const maxIndex = getMaxIndex();

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
    if (currentIndex < getMaxIndex()) {
        currentIndex++;
        updateCarousel();
    }
});


window.addEventListener('resize', updateCarousel);

updateCarousel();

