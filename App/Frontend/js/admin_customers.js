function renderCustomers(customers) {
    const tbody = document.getElementById('customerTableBody');
    tbody.innerHTML = '';

    if (!customers.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-muted">Keine Kunden vorhanden</td></tr>';
        return;
    }

    customers.forEach((customer) => {
        const active = Number(customer.active) === 1;
        const row = document.createElement('tr');
        row.innerHTML =
            '<td>' + customer.id + '</td>' +
            '<td>' + escapeHtml((customer.first_name || '') + ' ' + (customer.last_name || '')) + '</td>' +
            '<td>' + escapeHtml(customer.username) + '</td>' +
            '<td>' + escapeHtml(customer.email) + '</td>' +
            '<td>' + escapeHtml(customer.role) + '</td>' +
            '<td><span class="badge ' + (active ? 'text-bg-success' : 'text-bg-secondary') + '">' + (active ? 'Aktiv' : 'Deaktiviert') + '</span></td>' +
            '<td class="text-nowrap">' +
            '  <a class="btn btn-outline-secondary btn-sm" href="admin_orders.php?customer_id=' + customer.id + '">Bestellungen</a> ' +
            '  <button class="btn btn-outline-warning btn-sm" data-action="toggle" data-id="' + customer.id + '" data-active="' + (active ? '1' : '0') + '">Status ändern</button>' +
            '</td>';
        tbody.appendChild(row);
    });
}

async function loadCustomers() {
    const response = await fetch('../../Backend/logic/admin_get_customers.php');
    const payload = await response.json();
    if (payload.status !== 'success') {
        setMessage('customerMessage', payload.message || 'Kunden konnten nicht geladen werden', true);
        return;
    }
    renderCustomers(payload.data);
}

async function toggleCustomerStatus(id, currentActive) {
    const response = await fetch('../../Backend/logic/admin_toggle_user_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, active: currentActive ? 0 : 1 }),
    });
    const payload = await response.json();
    const ok = payload.status === 'success';
    setMessage('customerMessage', payload.message, !ok);
    if (ok) {
        await loadCustomers();
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    if (!(await ensureAdmin())) {
        return;
    }

    await loadCustomers();
    document.getElementById('customerTableBody').addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement) || target.dataset.action !== 'toggle') {
            return;
        }
        toggleCustomerStatus(Number(target.dataset.id), target.dataset.active === '1');
    });
});
