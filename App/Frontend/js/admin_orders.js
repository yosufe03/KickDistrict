const orderStatusLabels = {
    pending: 'Offen',
    confirmed: 'Bestätigt',
    paid: 'Bezahlt',
    shipped: 'Versendet',
    cancelled: 'Storniert',
};

function statusSelect(order) {
    const options = Object.entries(orderStatusLabels).map(([value, label]) => {
        const selected = order.status === value ? ' selected' : '';
        return '<option value="' + value + '"' + selected + '>' + label + '</option>';
    }).join('');

    return '<select class="form-select form-select-sm order-status-select" data-id="' + order.id + '">' + options + '</select>';
}

function renderOrders(orders) {
    const container = document.getElementById('ordersContainer');
    container.innerHTML = '';

    if (!orders.length) {
        container.innerHTML = '<div class="alert alert-info">Keine Bestellungen vorhanden</div>';
        return;
    }

    orders.forEach((order) => {
        const card = document.createElement('article');
        card.className = 'card mb-3';
        const items = order.items.map((item) => {
            return '<tr>' +
                '<td>' + escapeHtml(item.product_name) + '</td>' +
                '<td>' + item.quantity + '</td>' +
                '<td>' + formatPrice(item.unit_price) + '</td>' +
                '<td>' + formatPrice(Number(item.unit_price) * Number(item.quantity)) + '</td>' +
                '</tr>';
        }).join('');

        card.innerHTML =
            '<div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">' +
            '  <div><strong>Bestellung #' + order.id + '</strong><span class="text-muted ms-2">' + escapeHtml(order.order_date) + '</span></div>' +
            '  <div class="order-status-control">' + statusSelect(order) + '</div>' +
            '</div>' +
            '<div class="card-body">' +
            '  <div class="row g-3 mb-3">' +
            '    <div class="col-md-8"><strong>Kunde:</strong> ' + escapeHtml(order.customer_name || order.username) + ' (' + escapeHtml(order.email) + ')</div>' +
            '    <div class="col-md-4 text-md-end"><strong>Gesamt:</strong> ' + formatPrice(order.total_amount) + voucherSummary(order) + '</div>' +
            '  </div>' +
            '  <div class="table-responsive">' +
            '    <table class="table table-sm mb-0">' +
            '      <thead><tr><th>Produkt</th><th>Menge</th><th>Einzelpreis</th><th>Summe</th></tr></thead>' +
            '      <tbody>' + (items || '<tr><td colspan="4" class="text-muted">Keine Positionen</td></tr>') + '</tbody>' +
            '    </table>' +
            '  </div>' +
            '</div>';
        container.appendChild(card);
    });
}

function voucherSummary(order) {
    const discount = Number(order.discount_amount || 0);
    if (discount <= 0) {
        return '';
    }

    return '<div class="text-success small">Gutschein ' + escapeHtml(order.voucher_code || '') + ': -' + formatPrice(discount) + '</div>';
}

async function loadOrders() {
    const params = new URLSearchParams(window.location.search);
    const customerId = params.get('customer_id');
    const url = '../../Backend/logic/admin_get_orders.php' + (customerId ? '?customer_id=' + encodeURIComponent(customerId) : '');
    const response = await fetch(url);
    const payload = await response.json();
    if (payload.status !== 'success') {
        setMessage('orderMessage', payload.message || 'Bestellungen konnten nicht geladen werden', true);
        return;
    }
    renderOrders(payload.data);
}

async function updateOrderStatus(orderId, status) {
    const response = await fetch('../../Backend/logic/admin_update_order_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: orderId, status }),
    });
    const payload = await response.json();
    const ok = payload.status === 'success';
    setMessage('orderMessage', payload.message, !ok);
    if (ok) {
        await loadOrders();
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    if (!(await ensureAdmin())) {
        return;
    }

    await loadOrders();
    document.getElementById('ordersContainer').addEventListener('change', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLSelectElement) || !target.classList.contains('order-status-select')) {
            return;
        }
        updateOrderStatus(Number(target.dataset.id), target.value);
    });
});
