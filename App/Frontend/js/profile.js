async function loadProfile() {
    try {
        const response = await fetch('../../Backend/logic/userProfile.php');
        const payload = await response.json();

        if (payload.status !== 'success') {
            setMessage('profileMessage', payload.message || 'Profil konnte nicht geladen werden', true);
            return;
        }

        const data = payload.data;
        ['salutation', 'first_name', 'last_name', 'email', 'username'].forEach((key) => {
            const el = document.getElementById(key);
            if (el) {
                el.value = data[key] || '';
            }
        });
    } catch (_err) {
        setMessage('profileMessage', 'Profil konnte nicht geladen werden', true);
    }
}

async function submitProfile(event) {
    event.preventDefault();

    const form = document.getElementById('profileForm');
    const formData = new FormData(form);

    try {
        const response = await fetch('../../Backend/logic/updateProfile.php', {
            method: 'POST',
            body: formData,
        });

        const payload = await response.json();
        const ok = payload.status === 'success';
        setMessage('profileMessage', payload.message, !ok);

        if (ok) {
            form.querySelector('#current_password').value = '';
            form.querySelector('#new_password').value = '';
        }
    } catch (_err) {
        setMessage('profileMessage', 'Profil konnte nicht gespeichert werden', true);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('profileForm');
    if (!form) {
        return;
    }

    form.addEventListener('submit', submitProfile);
    loadProfile();
});

