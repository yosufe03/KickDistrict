function byId(id) {
    return document.getElementById(id);
}

function formatPrice(value) {
    const number = Number(value) || 0;
    return number.toFixed(2) + ' EUR';
}

const DEFAULT_PRODUCT_IMAGE =
    'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22640%22 height=%22480%22 viewBox=%220 0 640 480%22%3E%3Crect width=%22640%22 height=%22480%22 fill=%22%23eef1f5%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-family=%22Arial, sans-serif%22 font-size=%2232%22 fill=%227a8794%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22%3EKickDistrict%3C/text%3E%3C/svg%3E';

function resolveProductImage(image) {
    return image ? encodeURI(image) : DEFAULT_PRODUCT_IMAGE;
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

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function addAdminNav(isAdmin) {
    const nav = document.querySelector('.navbar-nav');
    if (!nav || document.getElementById('adminNavLink')) {
        return;
    }

    if (isAdmin) {
        const wrapper = document.createElement('div');
        wrapper.className = 'nav-item d-flex gap-2 flex-wrap';
        wrapper.id = 'adminNavLink';
        wrapper.innerHTML =
            '<a class="nav-link" href="admin_products.php">Produkt hinzufügen</a>' +
            '<a class="nav-link" href="admin_customers.php">Kunden</a>' +
            '<a class="nav-link" href="admin_orders.php">Bestellungen</a>';
        nav.insertBefore(wrapper, nav.firstChild);
    }
}

function updateNav(isLoggedIn, isAdmin = false) {
    const loggedInOnly = document.querySelectorAll('[data-auth="in"]');
    const guestOnly = document.querySelectorAll('[data-auth="out"]');

    loggedInOnly.forEach((node) => node.classList.toggle('hidden', !isLoggedIn));
    guestOnly.forEach((node) => node.classList.toggle('hidden', isLoggedIn));
    addAdminNav(Boolean(isAdmin));
}

async function fetchLoginStatus() {
    const response = await fetch('../../Backend/logic/checkLogin.php');
    if (!response.ok) {
        return { loggedin: false };
    }
    return response.json();
}

async function ensureAdmin() {
    const status = await fetchLoginStatus();
    if (!status.loggedin || !status.admin) {
        window.location.href = './login.php';
        return false;
    }
    return true;
}

async function initNav() {
    try {
        const status = await fetchLoginStatus();
        updateNav(Boolean(status.loggedin), Boolean(status.admin));
    } catch (_err) {
        updateNav(false);
    }
}

document.addEventListener('DOMContentLoaded', initNav);
