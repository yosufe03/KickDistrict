async function loadVouchers() {
    try {
        const response = await fetch('../../Backend/logic/get_vouchers.php');
        const payload = await response.json();

        if (payload.status !== 'success') {
            setMessage('voucherMessage', payload.message || 'Gutscheine konnten nicht geladen werden', true);
            return;
        }

        const tbody = document.getElementById('voucherTableBody');
        tbody.innerHTML = '';

        payload.data.forEach((voucher) => {
            const row = document.createElement('tr');
            row.innerHTML = '<td>' + voucher.code + '</td>' +
                '<td>' + voucher.value + ' EUR</td>' +
                '<td>' + voucher.expiry_date + '</td>' +
                '<td>' + voucher.status + '</td>';
            tbody.appendChild(row);
        });
    } catch (_err) {
        setMessage('voucherMessage', 'Gutscheine konnten nicht geladen werden', true);
    }
}

document.addEventListener('DOMContentLoaded', loadVouchers);

