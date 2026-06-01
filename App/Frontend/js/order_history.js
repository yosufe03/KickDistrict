function renderOrderHistory(orders) {
    const tbody = byId('orderTableBody');
    if (!tbody) {
        return;
    }

    tbody.innerHTML = '';
    if (!orders.length) {
        setMessage('orderMessage', 'Noch keine Bestellungen vorhanden.', true);
        return;
    }

    setMessage('orderMessage', '');

    orders.forEach((order) => {
        const itemList = order.items
            .map((item) => item.quantity + ' x ' + item.product_name + ' (' + formatPrice(item.unit_price) + ')')
            .join('<br>');

        const row = document.createElement('tr');
        row.innerHTML =
            '<td>#' + order.id + '</td>' +
            '<td>' + new Date(order.order_date).toLocaleDateString('de-DE') + '</td>' +
            '<td>' + order.status + '</td>' +
            '<td>' + (itemList || '-') + '</td>' +
            '<td>' + formatPrice(order.total_amount) + '</td>' +
            '<td><a class="btn btn-sm btn-outline-primary" href="invoice.php?order_id=' + order.id + '">Rechnung</a></td>';
        tbody.appendChild(row);
    });
}

async function loadOrderHistory() {
    try {
        const response = await fetch('../../Backend/logic/get_order_history.php');
        const payload = await response.json();
        if (payload.status !== 'success') {
            setMessage('orderMessage', payload.message || 'Bestellhistorie konnte nicht geladen werden', true);
            return;
        }

        renderOrderHistory(payload.data || []);
    } catch (_err) {
        setMessage('orderMessage', 'Bestellhistorie konnte nicht geladen werden', true);
    }
}

document.addEventListener('DOMContentLoaded', loadOrderHistory);

