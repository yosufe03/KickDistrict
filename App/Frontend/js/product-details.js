function getProductId() {
    const params = new URLSearchParams(window.location.search);
    return Number(params.get('id') || 0);
}

async function loadProductDetails() {
    const productId = getProductId();
    if (!productId) {
        setMessage('productDetailMessage', 'Produkt-ID fehlt', true);
        return;
    }

    try {
        const response = await fetch('../../Backend/logic/get_product_details.php?id=' + productId);
        const payload = await response.json();

        if (payload.status !== 'success') {
            setMessage('productDetailMessage', payload.message || 'Produkt nicht gefunden', true);
            return;
        }

        const product = payload.data;
        byId('productImage').src = resolveProductImage(product.image);
        byId('productImage').alt = product.name;
        byId('productName').textContent = product.name;
        byId('productPrice').textContent = formatPrice(product.price);
        byId('productDescription').textContent = product.description || 'Keine Beschreibung vorhanden.';
        byId('addToCartBtn').dataset.id = product.id;
    } catch (_err) {
        setMessage('productDetailMessage', 'Produktdetails konnten nicht geladen werden', true);
    }
}

async function addDetailToCart(productId) {
    try {
        const response = await fetch('../../Backend/logic/add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, quantity: 1 }),
        });
        const payload = await response.json();
        const ok = payload.status === 'success';
        setMessage('productDetailMessage', payload.message, !ok);
    } catch (_err) {
        setMessage('productDetailMessage', 'Produkt konnte nicht hinzugefügt werden', true);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const addButton = byId('addToCartBtn');
    if (addButton) {
        addButton.addEventListener('click', () => {
            const productId = Number(addButton.dataset.id || 0);
            if (productId) {
                addDetailToCart(productId);
            }
        });
    }

    loadProductDetails();
});
