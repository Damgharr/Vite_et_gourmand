const button = document.getElementById('load-more');
if (button) {
    let offset = 6;
    button.addEventListener('click', async () => {
        const url = new URL(button.dataset.url, window.location.origin);
        url.searchParams.set('offset', offset);
        const current = new URL(window.location.href);
        ['minPrice', 'maxPrice', 'theme', 'diet', 'minPeople'].forEach(p => {
            const v = current.searchParams.get(p);
            if (v) url.searchParams.set(p, v);
        });
        const res = await fetch(url);
        const data = await res.json();
        document.getElementById('menus').insertAdjacentHTML('beforeend', data.html);
        offset += 6;
        if (!data.hasMore) {
            button.textContent = "Il n'y a plus de menus à afficher.";
            button.disabled = true;
        }
    });
}
