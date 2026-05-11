async function loadCategories() {
    const select = byId('categorySelect');
    if (!select) {
        return;
    }

    try {
        const response = await fetch('../../Backend/logic/get_categories.php');
        const payload = await response.json();
        if (payload.status !== 'success') {
            return;
        }

        payload.data.forEach((category) => {
            const option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.name;
            select.appendChild(option);
        });
    } catch (_err) {
        // Ignore category loading errors, products can still be shown.
    }
}

function buildProductCard(product) {
    const col = document.createElement('div');
    col.className = 'col-sm-6 col-lg-4';

    const image = product.image || '../res/img/product-placeholder.svg';
    col.innerHTML =
        '<div class="card h-100 shadow-sm">' +
        '  <img class="card-img-top product-image" src="' + image + '" alt="' + product.name + '">' +
        '  <div class="card-body d-flex flex-column">' +
        '    <h5 class="card-title">' + product.name + '</h5>' +
        '    <p class="card-text mb-3 text-muted">' + formatPrice(product.price) + '</p>' +
        '    <div class="mt-auto d-flex gap-2">' +
        '      <a class="btn btn-outline-secondary btn-sm" href="product.php?id=' + product.id + '">Details</a>' +
        '      <button class="btn btn-primary btn-sm" data-action="add" data-id="' + product.id + '">In den Warenkorb</button>' +
        '    </div>' +
        '  </div>' +
        '</div>';

    return col;
}

async function loadProducts() {
    const grid = byId('productGrid');
    const message = byId('productMessage');
    if (!grid) {
        return;
    }

    const searchInput = byId('searchInput');
    const categorySelect = byId('categorySelect');
    const params = new URLSearchParams();

    if (searchInput && searchInput.value.trim() !== '') {
        params.set('q', searchInput.value.trim());
    }

    if (categorySelect && categorySelect.value) {
        params.set('category_id', categorySelect.value);
    }

    const url = '../../Backend/logic/get_products.php' + (params.toString() ? '?' + params.toString() : '');

    try {
        const response = await fetch(url);
        const payload = await response.json();
        if (payload.status !== 'success') {
            setMessage('productMessage', payload.message || 'Produkte konnten nicht geladen werden', true);
            return;
        }

        grid.innerHTML = '';
        if (!payload.data.length) {
            if (message) {
                setMessage('productMessage', 'Keine Produkte gefunden', true);
            }
            return;
        }

        payload.data.forEach((product) => {
            grid.appendChild(buildProductCard(product));
        });
        setMessage('productMessage', '');
    } catch (_err) {
        setMessage('productMessage', 'Produkte konnten nicht geladen werden', true);
    }
}

async function addToCart(productId) {
    try {
        const response = await fetch('../../Backend/logic/add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, quantity: 1 }),
        });
        const payload = await response.json();
        const ok = payload.status === 'success';
        setMessage('productMessage', payload.message, !ok);
    } catch (_err) {
        setMessage('productMessage', 'Produkt konnte nicht hinzugefügt werden', true);
    }
}

function bindProductActions() {
    const grid = byId('productGrid');
    if (!grid) {
        return;
    }

    grid.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) {
            return;
        }

        if (target.dataset.action === 'add') {
            const productId = Number(target.dataset.id || 0);
            if (productId > 0) {
                addToCart(productId);
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const form = byId('searchForm');
    if (form) {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            loadProducts();
        });
    }

    const categorySelect = byId('categorySelect');
    if (categorySelect) {
        categorySelect.addEventListener('change', loadProducts);
    }

    loadCategories().then(loadProducts);
    bindProductActions();
});
