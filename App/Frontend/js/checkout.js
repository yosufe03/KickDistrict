let checkoutSubtotal = 0;
let appliedVoucher = null;

function renderCheckoutItems(items) {
    const tbody = byId('checkoutTableBody');
    if (!tbody) {
        return;
    }

    tbody.innerHTML = '';
    if (!items.length) {
        setMessage('checkoutMessage', 'Warenkorb ist leer', true);
        updateCheckoutTotals(0);
        setPlaceOrderEnabled(false);
        return;
    }

    setMessage('checkoutMessage', '');

    items.forEach((item) => {
        const row = document.createElement('tr');
        const subtotal = Number(item.price) * Number(item.quantity);
        row.innerHTML =
            '<td>' + escapeHtml(item.name) + '</td>' +
            '<td>' + formatPrice(item.price) + '</td>' +
            '<td>' + item.quantity + '</td>' +
            '<td>' + formatPrice(subtotal) + '</td>';
        tbody.appendChild(row);
    });

    checkoutSubtotal = items.reduce((sum, item) => sum + Number(item.price) * Number(item.quantity), 0);
    appliedVoucher = null;
    clearVoucherMessageAndInput();
    updateCheckoutTotals(checkoutSubtotal);
    setPlaceOrderEnabled(true);
}

function clearCheckoutTable() {
    const tbody = byId('checkoutTableBody');
    if (tbody) {
        tbody.innerHTML = '';
    }
    checkoutSubtotal = 0;
    appliedVoucher = null;
    updateCheckoutTotals(0);
    setPlaceOrderEnabled(false);
}

function updateCheckoutTotals(total) {
    const subtotalEl = byId('checkoutSubtotal');
    const totalEl = byId('checkoutTotal');
    const discountRow = byId('checkoutDiscountRow');
    const discountEl = byId('checkoutDiscount');
    const discount = appliedVoucher ? Number(appliedVoucher.discount_amount) : 0;

    if (subtotalEl) {
        subtotalEl.textContent = formatPrice(total);
    }
    if (discountRow && discountEl) {
        discountRow.classList.toggle('hidden', discount <= 0);
        discountEl.textContent = '-' + formatPrice(discount);
    }
    if (totalEl) {
        totalEl.textContent = formatPrice(Math.max(total - discount, 0));
    }
}

function clearVoucherMessageAndInput() {
    const input = byId('voucherCodeInput');
    if (input) {
        input.value = '';
    }
    setMessage('voucherCheckoutMessage', '');
}

function currentVoucherCode() {
    const input = byId('voucherCodeInput');
    return input ? input.value.trim().toUpperCase() : '';
}

function setPlaceOrderEnabled(enabled) {
    const button = byId('placeOrderBtn');
    if (!button) {
        return;
    }

    button.disabled = !enabled;
}

async function loadCheckout() {
    try {
        const status = await fetchLoginStatus();
        if (!status.loggedin) {
            setPlaceOrderEnabled(false);
            setMessage('checkoutMessage', 'Bitte melde dich an, um zu bestellen.', true);
            const actions = byId('checkoutActions');
            if (actions) {
                actions.classList.remove('hidden');
                actions.innerHTML = '<a class="btn btn-outline-primary w-100" href="login.php">Zum Login</a>';
            }
            return;
        }

        const response = await fetch('../../Backend/logic/get_cart_items.php');
        const payload = await response.json();
        if (payload.status !== 'success') {
            setMessage('checkoutMessage', payload.message || 'Warenkorb konnte nicht geladen werden', true);
            setPlaceOrderEnabled(false);
            return;
        }

        renderCheckoutItems(payload.data);
    } catch (_err) {
        setMessage('checkoutMessage', 'Checkout konnte nicht geladen werden', true);
        setPlaceOrderEnabled(false);
    }
}

async function applyVoucher() {
    const input = byId('voucherCodeInput');
    const code = currentVoucherCode();
    if (!code) {
        appliedVoucher = null;
        updateCheckoutTotals(checkoutSubtotal);
        setMessage('voucherCheckoutMessage', 'Bitte Gutscheincode eingeben', true);
        return;
    }

    try {
        const response = await fetch('../../Backend/logic/validate_voucher.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ code }),
        });
        const text = await response.text();
        let payload;
        try {
            payload = JSON.parse(text);
        } catch (_parseError) {
            setMessage('voucherCheckoutMessage', 'Gutschein konnte nicht geprüft werden: Serverantwort ist ungültig', true);
            return;
        }

        if (payload.status !== 'success') {
            appliedVoucher = null;
            updateCheckoutTotals(checkoutSubtotal);
            setMessage('voucherCheckoutMessage', payload.message || 'Gutschein ungültig', true);
            return;
        }

        appliedVoucher = payload.data;
        if (input) {
            input.value = appliedVoucher.code;
        }
        updateCheckoutTotals(checkoutSubtotal);
        setMessage('voucherCheckoutMessage', payload.message, false);
    } catch (_err) {
        appliedVoucher = null;
        updateCheckoutTotals(checkoutSubtotal);
        setMessage('voucherCheckoutMessage', 'Gutschein konnte nicht geprüft werden', true);
    }
}

async function placeOrder() {
    const button = byId('placeOrderBtn');
    if (button) {
        button.disabled = true;
    }

    try {
        const response = await fetch('../../Backend/logic/place_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ voucher_code: currentVoucherCode() }),
        });
        const payload = await response.json();
        if (payload.status !== 'success') {
            setMessage('checkoutMessage', payload.message || 'Bestellung konnte nicht aufgegeben werden', true);
            setPlaceOrderEnabled(true);
            return;
        }

        const orderId = payload.data?.order_id;
        setMessage('checkoutMessage', 'Bestellung erfolgreich! Vielen Dank.', false);
        const actions = byId('checkoutActions');
        if (actions) {
            actions.classList.remove('hidden');
            actions.innerHTML =
                '<div class="d-grid gap-2">' +
                '  <a class="btn btn-outline-primary" href="invoice.php?order_id=' + orderId + '">Rechnung anzeigen</a>' +
                '  <a class="btn btn-link" href="order_history.php">Zur Bestellhistorie</a>' +
                '</div>';
        }

        clearCheckoutTable();
    } catch (_err) {
        setMessage('checkoutMessage', 'Bestellung konnte nicht aufgegeben werden', true);
        setPlaceOrderEnabled(true);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const button = byId('placeOrderBtn');
    if (button) {
        button.addEventListener('click', placeOrder);
    }

    const voucherButton = byId('applyVoucherBtn');
    if (voucherButton) {
        voucherButton.addEventListener('click', applyVoucher);
    }

    const voucherInput = byId('voucherCodeInput');
    if (voucherInput) {
        voucherInput.addEventListener('input', () => {
            appliedVoucher = null;
            updateCheckoutTotals(checkoutSubtotal);
            setMessage('voucherCheckoutMessage', '');
        });
        voucherInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                applyVoucher();
            }
        });
    }

    loadCheckout();
});
