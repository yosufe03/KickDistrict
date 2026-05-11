async function loadCart() {
    try {
        const response = await fetch('../../Backend/logic/get_cart_items.php');
        const payload = await response.json();
        if (payload.status !== 'success') {
            setMessage('cartMessage', payload.message || 'Warenkorb konnte nicht geladen werden', true);
            return;
        }

        renderCart(payload.data);
    } catch (_err) {
        setMessage('cartMessage', 'Warenkorb konnte nicht geladen werden', true);
    }
}

function renderCart(items) {
    const tbody = byId('cartTableBody');
    if (!tbody) {
        return;
    }

    tbody.innerHTML = '';
    if (!items.length) {
        setMessage('cartMessage', 'Warenkorb ist leer', true);
        updateTotal(0);
        return;
    }

    setMessage('cartMessage', '');

    items.forEach((item) => {
        const row = document.createElement('tr');
        const subtotal = Number(item.price) * Number(item.quantity);

        row.innerHTML =
            '<td>' + item.name + '</td>' +
            '<td>' + formatPrice(item.price) + '</td>' +
            '<td>' +
            '  <div class="qty-group">' +
            '    <button class="btn btn-outline-secondary btn-sm" data-action="decrease" data-id="' + item.product_id + '">-</button>' +
            '    <span>' + item.quantity + '</span>' +
            '    <button class="btn btn-outline-secondary btn-sm" data-action="increase" data-id="' + item.product_id + '">+</button>' +
            '  </div>' +
            '</td>' +
            '<td>' + formatPrice(subtotal) + '</td>' +
            '<td><button class="btn btn-outline-danger btn-sm" data-action="remove" data-id="' + item.product_id + '">Entfernen</button></td>';

        tbody.appendChild(row);
    });

    const total = items.reduce((sum, item) => sum + Number(item.price) * Number(item.quantity), 0);
    updateTotal(total);
}

function updateTotal(total) {
    const totalEl = byId('cartTotal');
    if (totalEl) {
        totalEl.textContent = formatPrice(total);
    }
}

async function updateQuantity(productId, quantity) {
    try {
        const response = await fetch('../../Backend/logic/update_cart_item.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, quantity }),
        });
        const payload = await response.json();
        if (payload.status !== 'success') {
            setMessage('cartMessage', payload.message || 'Menge konnte nicht aktualisiert werden', true);
            return;
        }
        loadCart();
    } catch (_err) {
        setMessage('cartMessage', 'Menge konnte nicht aktualisiert werden', true);
    }
}

async function removeItem(productId) {
    try {
        const response = await fetch('../../Backend/logic/remove_from_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId }),
        });
        const payload = await response.json();
        if (payload.status !== 'success') {
            setMessage('cartMessage', payload.message || 'Artikel konnte nicht entfernt werden', true);
            return;
        }
        loadCart();
    } catch (_err) {
        setMessage('cartMessage', 'Artikel konnte nicht entfernt werden', true);
    }
}

function bindCartActions() {
    const tbody = byId('cartTableBody');
    if (!tbody) {
        return;
    }

    tbody.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) {
            return;
        }

        const action = target.dataset.action;
        const productId = Number(target.dataset.id || 0);
        if (!action || !productId) {
            return;
        }

        if (action === 'remove') {
            removeItem(productId);
            return;
        }

        const quantityCell = target.closest('tr')?.querySelector('span');
        const currentQty = Number(quantityCell?.textContent || 0);
        if (action === 'increase') {
            updateQuantity(productId, currentQty + 1);
        }
        if (action === 'decrease' && currentQty > 1) {
            updateQuantity(productId, currentQty - 1);
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    bindCartActions();
    loadCart();
});

