function generateVoucherCode() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = '';
    for (let i = 0; i < 8; i += 1) {
        code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return code;
}

async function createVoucher(event) {
    event.preventDefault();
    const form = document.getElementById('voucherCreateForm');
    const data = Object.fromEntries(new FormData(form).entries());

    if (!data.code) {
        data.code = generateVoucherCode();
    }

    const response = await fetch('../../Backend/logic/admin_create_voucher.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data),
    });
    const payload = await response.json();
    const ok = payload.status === 'success';
    const code = payload.data && payload.data.code ? ' Code: ' + payload.data.code : '';
    setMessage('voucherCreateMessage', (payload.message || '') + code, !ok);

    if (ok) {
        form.reset();
        await loadVouchers();
    }
}

async function initVoucherAdminPanel() {
    try {
        const status = await fetchLoginStatus();
        if (!status.admin) {
            return;
        }

        document.getElementById('voucherAdminPanel').classList.remove('hidden');
        document.getElementById('generateVoucherBtn').addEventListener('click', () => {
            document.getElementById('voucherCode').value = generateVoucherCode();
        });
        document.getElementById('voucherCreateForm').addEventListener('submit', createVoucher);
    } catch (_err) {
        // Public voucher list remains usable if auth status cannot be loaded.
    }
}

document.addEventListener('DOMContentLoaded', initVoucherAdminPanel);
