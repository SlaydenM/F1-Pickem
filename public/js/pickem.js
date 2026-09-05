const bets = [0, 0, 0];
let selectedDriver = null;

function submitBets() {
    if (bets.includes(0)) {
        console.error('Please choose all three drivers before submitting.');
        return;
    }

    const payload = {
        bets: bets,
        bonus: window.PICKEM_BONUS,
    };

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    fetch(window.PICKEM_SUBMIT_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ bettor: JSON.stringify(payload) }),
    })
        .then((response) => {
            if (!response.ok) {
                return response.text().then((text) => Promise.reject(text));
            }
            return response.json();
        })
        .then(() => {
            window.location.replace(`/picks/view?sessionKey=${window.PICKEM_SESSION_KEY}`);
        })
        .catch((error) => {
            console.error('Failed to set session bet:', error);
        });
}

function refreshSessionKey(sessionKey) {
    const route = window.REFRESH_ROUTE ?? '/picks/view';
    window.location.href = `${route}?sessionKey=${sessionKey}`;
}

function snapTo($element, position) {
    $element.style.left = `${position.left}px`;
    $element.style.top = `${position.top}px`;
}

function isSnapped(dragPos, snapPos) {
    return Math.abs(dragPos.top - snapPos.top) <= 50 && Math.abs(dragPos.left - snapPos.left) <= 100;
}

function makeDraggable(element) {
    let offset = { x: 0, y: 0 };
    let dragging = false;

    element.addEventListener('pointerdown', (event) => {
        dragging = true;
        element.setPointerCapture(event.pointerId);
        offset.x = event.clientX - element.getBoundingClientRect().left;
        offset.y = event.clientY - element.getBoundingClientRect().top;
        element.style.position = 'absolute';
        element.style.zIndex = 999;
    });

    element.addEventListener('pointermove', (event) => {
        if (!dragging) {
            return;
        }
        element.style.left = `${event.clientX - offset.x}px`;
        element.style.top = `${event.clientY - offset.y}px`;
    });

    element.addEventListener('pointerup', () => {
        dragging = false;
        const dragPos = element.getBoundingClientRect();
        let snapped = false;

        document.querySelectorAll('.bet-box').forEach((target, index) => {
            const snapPos = target.getBoundingClientRect();
            if (isSnapped(dragPos, snapPos)) {
                snapTo(element, snapPos);
                bets[index] = parseInt(element.dataset.number, 10);
                snapped = true;
            }
        });

        if (!snapped) {
            element.style.position = '';
            element.style.left = '';
            element.style.top = '';
            element.style.zIndex = '';
        }
    });
}

window.addEventListener('DOMContentLoaded', () => {
    document.getElementById('submit-button')?.addEventListener('click', submitBets);
    document.querySelectorAll('.draggable').forEach((element) => makeDraggable(element));
});
