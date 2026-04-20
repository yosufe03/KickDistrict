async function submitRegisterForm(event) {
    event.preventDefault();

    const form = document.getElementById('registerForm');
    const data = Object.fromEntries(new FormData(form).entries());

    try {
        const response = await fetch('../../Backend/logic/register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
        });

        const payload = await response.json();
        const ok = payload.status === 'success';
        setMessage('registerMessage', payload.message, !ok);

        if (ok) {
            form.reset();
        }
    } catch (_err) {
        setMessage('registerMessage', 'Registrierung konnte nicht gesendet werden', true);
    }
}

async function submitLoginForm(event) {
    event.preventDefault();

    const form = document.getElementById('loginForm');
    const data = Object.fromEntries(new FormData(form).entries());
    data.remember_me = Boolean(data.remember_me);

    try {
        const response = await fetch('../../Backend/logic/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
        });

        const payload = await response.json();
        const ok = payload.status === 'success';
        setMessage('loginMessage', payload.message, !ok);

        if (ok) {
            window.location.href = './profile.php';
        }
    } catch (_err) {
        setMessage('loginMessage', 'Login konnte nicht gesendet werden', true);
    }
}

async function logout() {
    try {
        await fetch('../../Backend/logic/logout.php');
    } finally {
        window.location.href = './index.php';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', submitRegisterForm);
    }

    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', submitLoginForm);
    }

    const logoutButton = document.getElementById('logoutBtn');
    if (logoutButton) {
        logoutButton.addEventListener('click', logout);
    }
});
