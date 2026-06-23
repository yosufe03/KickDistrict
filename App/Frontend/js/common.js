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

function navLink(label, href) {
    return '<a class="nav-link" href="' + href + '">' + label + '</a>';
}

function navButton(label, id, className = 'btn btn-outline-danger btn-sm') {
    return '<button id="' + id + '" class="' + className + '" type="button">' + label + '</button>';
}

function renderNav(items) {
    const nav = document.querySelector('.navbar-nav');
    if (!nav) {
        return;
    }

    nav.innerHTML = items
        .map((item) => {
            if (item.type === 'button') {
                return navButton(item.label, item.id, item.className);
            }

            return navLink(item.label, item.href);
        })
        .join('');
}

function updateNav(isLoggedIn, isAdmin = false) {
    if (isAdmin) {
        renderNav([
            { label: 'Home', href: 'index.php' },
            { label: 'Produkte bearbeiten', href: 'admin_products.php' },
            { label: 'Kunden bearbeiten', href: 'admin_customers.php' },
            { label: 'Gutscheine verwalten', href: 'vouchers.php' },
            { type: 'button', label: 'Logout', id: 'logoutBtn' },
        ]);
        return;
    }

    if (isLoggedIn) {
        renderNav([
            { label: 'Home', href: 'index.php' },
            { label: 'Produkte', href: 'index.php' },
            { label: 'Mein Konto', href: 'profile.php' },
            { label: 'Warenkorb', href: 'cart.php' },
            { type: 'button', label: 'Logout', id: 'logoutBtn' },
        ]);
        return;
    }

    renderNav([
        { label: 'Home', href: 'index.php' },
        { label: 'Produkte', href: 'index.php' },
        { label: 'Warenkorb', href: 'cart.php' },
        { label: 'Login', href: 'login.php' },
        { label: 'Registrieren', href: 'register.php' },
    ]);
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

document.addEventListener('click', async (event) => {
    if (event.target?.id !== 'logoutBtn') {
        return;
    }

    try {
        await fetch('../../Backend/logic/logout.php');
    } finally {
        window.location.href = './index.php';
    }
});
