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

    const csrfToken = window.CSRF_TOKEN || document.querySelector('meta[name="csrf-token"]')?.content;

    $.ajax({
        url: window.PICKEM_SUBMIT_URL,
        method: 'POST',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
        },
        data: {
            bettor: JSON.stringify(payload),
        },
        success(response) {
            console.log('Session bet set successfully!', response);
            window.location.replace(`/picks/view?sessionKey=${window.PICKEM_SESSION_KEY}`);
        },
        error(xhr) {
            console.error('Failed to set session bet:', xhr.responseText || xhr.statusText);
        },
    });
}

function refreshSessionKey(sessionKey) {
    const route = window.REFRESH_ROUTE ?? '/picks/view';
    window.location.href = `${route}?sessionKey=${sessionKey}`;
}

function snapTo($element, position) {
    $element.css({
        left: `${position.left}px`,
        top: `${position.top}px`,
    });
}

function isSnapped(dragPos, snapPos) {
    return Math.abs(dragPos.top - snapPos.top) <= 50 && Math.abs(dragPos.left - snapPos.left) <= 100;
}

$(function () {
    $('#submit-button').on('click', submitBets);

    $('.draggable').each(function () {
        const $driver = $(this);
        $driver.draggable({
            containment: 'body',
            stack: '.draggable',
            start() {
                selectedDriver = $driver;
            },
            stop() {
                const dragPos = $driver.offset();
                let snapped = false;

                $('.bet-box').each(function (index) {
                    const snapPos = $(this).offset();
                    if (isSnapped(dragPos, snapPos)) {
                        snapTo($driver, snapPos);
                        bets[index] = parseInt($driver.data('number'), 10);
                        snapped = true;
                    }
                });

                if (!snapped) {
                    $driver.css({ position: 'relative', left: '', top: '' });
                }
            },
        });
    });
});
