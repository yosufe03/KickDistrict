function renderCheckoutItems(items) {
    const tbody = byId('checkoutTableBody');
    if (!tbody) {
        return;
    }

    tbody.innerHTML = '';
    if (!items.length) {
        setMessage('checkoutMessage', 'Warenkorb ist leer', true);
        updateCheckoutTotal(0);
        setPlaceOrderEnabled(false);
        return;
    }

    setMessage('checkoutMessage', '');

    items.forEach((item) => {
        const row = document.createElement('tr');
        const subtotal = Number(item.price) * Number(item.quantity);
        row.innerHTML =
            '<td>' + item.name + '</td>' +
            '<td>' + formatPrice(item.price) + '</td>' +
            '<td>' + item.quantity + '</td>' +
            '<td>' + formatPrice(subtotal) + '</td>';
        tbody.appendChild(row);
    });

    const total = items.reduce((sum, item) => sum + Number(item.price) * Number(item.quantity), 0);
    updateCheckoutTotal(total);
    setPlaceOrderEnabled(true);
}

function clearCheckoutTable() {
    const tbody = byId('checkoutTableBody');
    if (tbody) {
        tbody.innerHTML = '';
    }
    updateCheckoutTotal(0);
    setPlaceOrderEnabled(false);
}

function updateCheckoutTotal(total) {
    const totalEl = byId('checkoutTotal');
    if (totalEl) {
        totalEl.textContent = formatPrice(total);
    }
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

async function placeOrder() {
    const button = byId('placeOrderBtn');
    if (button) {
        button.disabled = true;
    }

    try {
        const response = await fetch('../../Backend/logic/place_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({}),
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

    loadCheckout();
});
