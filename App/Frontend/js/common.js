function byId(id) {
    return document.getElementById(id);
}

function setMessage(id, text, isError = false) {
    const el = byId(id);
    if (!el) {
        return;
    }

    el.textContent = text || '';
    el.className = isError ? 'alert alert-danger mt-3' : 'alert alert-success mt-3';
    if (!text) {
        el.className = 'hidden';
    }
}

function updateNav(isLoggedIn) {
    const loggedInOnly = document.querySelectorAll('[data-auth="in"]');
    const guestOnly = document.querySelectorAll('[data-auth="out"]');

    loggedInOnly.forEach((node) => node.classList.toggle('hidden', !isLoggedIn));
    guestOnly.forEach((node) => node.classList.toggle('hidden', isLoggedIn));
}

async function fetchLoginStatus() {
    const response = await fetch('../../Backend/logic/checkLogin.php');
    if (!response.ok) {
        return { loggedin: false };
    }
    return response.json();
}

async function initNav() {
    try {
        const status = await fetchLoginStatus();
        updateNav(Boolean(status.loggedin));
    } catch (_err) {
        updateNav(false);
    }
}

document.addEventListener('DOMContentLoaded', initNav);

