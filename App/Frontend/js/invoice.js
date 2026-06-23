function renderInvoice(data) {
    const invoiceNumber = byId('invoiceNumber');
    const invoiceDate = byId('invoiceDate');
    const invoiceCustomer = byId('invoiceCustomer');
    const invoiceEmail = byId('invoiceEmail');
    const tbody = byId('invoiceTableBody');
    const subtotalEl = byId('invoiceSubtotal');
    const totalEl = byId('invoiceTotal');
    const voucherRow = byId('invoiceVoucherRow');
    const discountEl = byId('invoiceDiscount');
    const voucherCodeEl = byId('invoiceVoucherCode');

    if (!tbody || !totalEl) {
        return;
    }

    if (invoiceNumber) {
        invoiceNumber.textContent = data.invoice_number;
    }
    if (invoiceDate) {
        invoiceDate.textContent = new Date(data.order.order_date).toLocaleDateString('de-DE');
    }
    if (invoiceCustomer) {
        invoiceCustomer.textContent = data.customer.name;
    }
    if (invoiceEmail) {
        invoiceEmail.textContent = data.customer.email;
    }

    tbody.innerHTML = '';
    let subtotalTotal = 0;
    data.items.forEach((item) => {
        const row = document.createElement('tr');
        const subtotal = Number(item.unit_price) * Number(item.quantity);
        subtotalTotal += subtotal;
        row.innerHTML =
            '<td>' + escapeHtml(item.product_name) + '</td>' +
            '<td>' + formatPrice(item.unit_price) + '</td>' +
            '<td>' + item.quantity + '</td>' +
            '<td>' + formatPrice(subtotal) + '</td>';
        tbody.appendChild(row);
    });

    const discount = Number(data.order.discount_amount || 0);
    const voucherCode = data.order.voucher_code || '';
    if (subtotalEl) {
        subtotalEl.textContent = formatPrice(subtotalTotal);
    }
    if (voucherRow && discountEl && voucherCodeEl) {
        voucherRow.classList.toggle('hidden', !voucherCode && discount <= 0);
        discountEl.textContent = formatPrice(discount);
        voucherCodeEl.textContent = voucherCode ? '(' + voucherCode + ')' : '';
    }
    totalEl.textContent = formatPrice(data.order.total_amount);
}

async function loadInvoice() {
    const params = new URLSearchParams(window.location.search);
    const orderId = Number(params.get('order_id') || 0);
    const printBtn = byId('printInvoiceBtn');

    if (!orderId) {
        setMessage('invoiceMessage', 'Keine Bestellnummer gefunden.', true);
        if (printBtn) {
            printBtn.disabled = true;
        }
        return;
    }

    try {
        const response = await fetch('../../Backend/logic/get_invoice.php?order_id=' + orderId);
        const payload = await response.json();
        if (payload.status !== 'success') {
            setMessage('invoiceMessage', payload.message || 'Rechnung konnte nicht geladen werden', true);
            if (printBtn) {
                printBtn.disabled = true;
            }
            return;
        }

        renderInvoice(payload.data);
    } catch (_err) {
        setMessage('invoiceMessage', 'Rechnung konnte nicht geladen werden', true);
        if (printBtn) {
            printBtn.disabled = true;
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const printBtn = byId('printInvoiceBtn');
    if (printBtn) {
        printBtn.addEventListener('click', () => window.print());
    }

    loadInvoice();
});
