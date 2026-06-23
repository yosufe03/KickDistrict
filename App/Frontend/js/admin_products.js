const productForm = document.getElementById('productForm');
const categorySelect = document.getElementById('adminCategorySelect');
let adminProducts = [];
let adminCategories = [];

async function loadAdminCategories() {
    const response = await fetch('../../Backend/logic/get_categories.php');
    const payload = await response.json();
    if (payload.status !== 'success') {
        setMessage('adminProductMessage', 'Kategorien konnten nicht geladen werden', true);
        return;
    }

    adminCategories = payload.data;
    categorySelect.innerHTML = '<option value="">Kategorie wählen</option>';
    adminCategories.forEach((category) => {
        const option = document.createElement('option');
        option.value = category.id;
        option.textContent = category.name;
        categorySelect.appendChild(option);
    });
}

function categoryName(categoryId) {
    const category = adminCategories.find((item) => Number(item.id) === Number(categoryId));
    return category ? category.name : 'Keine Kategorie';
}

function resetProductForm() {
    productForm.reset();
    document.getElementById('productId').value = '';
    document.getElementById('productImagePath').value = '';
    document.getElementById('productFormTitle').textContent = 'Produkt hinzufügen';
    document.getElementById('productSubmitBtn').textContent = 'Produkt speichern';
}

function fillProductForm(product) {
    document.getElementById('productId').value = product.id;
    document.getElementById('productName').value = product.name;
    document.getElementById('productDescription').value = product.description || '';
    document.getElementById('productPrice').value = product.price;
    document.getElementById('adminCategorySelect').value = product.category_id || '';
    document.getElementById('productImagePath').value = product.image || '';
    document.getElementById('productFormTitle').textContent = 'Produkt bearbeiten';
    document.getElementById('productSubmitBtn').textContent = 'Produkt aktualisieren';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function renderProducts() {
    const tbody = document.getElementById('adminProductTableBody');
    tbody.innerHTML = '';

    if (!adminProducts.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-muted">Keine Produkte vorhanden</td></tr>';
        return;
    }

    adminProducts.forEach((product) => {
        const row = document.createElement('tr');
        const image = product.image || '../res/img/product-placeholder.svg';
        row.innerHTML =
            '<td><img class="admin-thumb" src="' + escapeHtml(image) + '" alt=""></td>' +
            '<td><strong>' + escapeHtml(product.name) + '</strong><div class="text-muted small">' + escapeHtml(product.description || '') + '</div></td>' +
            '<td>' + formatPrice(product.price) + '</td>' +
            '<td>' + escapeHtml(categoryName(product.category_id)) + '</td>' +
            '<td class="text-nowrap">' +
            '  <button class="btn btn-outline-primary btn-sm" data-action="edit" data-id="' + product.id + '">Bearbeiten</button> ' +
            '  <button class="btn btn-outline-danger btn-sm" data-action="delete" data-id="' + product.id + '">Löschen</button>' +
            '</td>';
        tbody.appendChild(row);
    });
}

async function loadAdminProducts() {
    const response = await fetch('../../Backend/logic/get_products.php');
    const payload = await response.json();
    if (payload.status !== 'success') {
        setMessage('adminProductMessage', payload.message || 'Produkte konnten nicht geladen werden', true);
        return;
    }

    adminProducts = payload.data;
    renderProducts();
}

async function uploadSelectedImage() {
    const fileInput = document.getElementById('productImage');
    if (!fileInput.files.length) {
        return document.getElementById('productImagePath').value;
    }

    const formData = new FormData();
    formData.append('image', fileInput.files[0]);
    const response = await fetch('../../Backend/logic/admin_upload_image.php', {
        method: 'POST',
        body: formData,
    });
    const payload = await response.json();
    if (payload.status !== 'success') {
        throw new Error(payload.message || 'Bild konnte nicht hochgeladen werden');
    }
    return payload.data.image;
}

async function saveProduct(event) {
    event.preventDefault();

    try {
        const image = await uploadSelectedImage();
        const id = Number(document.getElementById('productId').value || 0);
        const data = {
            id,
            name: document.getElementById('productName').value,
            description: document.getElementById('productDescription').value,
            price: document.getElementById('productPrice').value,
            category_id: document.getElementById('adminCategorySelect').value,
            image,
        };

        const endpoint = id > 0 ? 'admin_update_product.php' : 'admin_add_product.php';
        const response = await fetch('../../Backend/logic/' + endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
        });
        const payload = await response.json();
        const ok = payload.status === 'success';
        setMessage('adminProductMessage', payload.message, !ok);
        if (ok) {
            resetProductForm();
            await loadAdminProducts();
        }
    } catch (error) {
        setMessage('adminProductMessage', error.message || 'Produkt konnte nicht gespeichert werden', true);
    }
}

async function deleteProduct(productId) {
    if (!window.confirm('Produkt wirklich löschen?')) {
        return;
    }

    const response = await fetch('../../Backend/logic/admin_delete_product.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: productId }),
    });
    const payload = await response.json();
    const ok = payload.status === 'success';
    setMessage('adminProductMessage', payload.message, !ok);
    if (ok) {
        await loadAdminProducts();
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    if (!(await ensureAdmin())) {
        return;
    }

    await loadAdminCategories();
    await loadAdminProducts();

    productForm.addEventListener('submit', saveProduct);
    document.getElementById('productResetBtn').addEventListener('click', resetProductForm);
    document.getElementById('reloadProductsBtn').addEventListener('click', loadAdminProducts);
    document.getElementById('adminProductTableBody').addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) {
            return;
        }
        const productId = Number(target.dataset.id || 0);
        if (target.dataset.action === 'edit') {
            const product = adminProducts.find((item) => Number(item.id) === productId);
            if (product) {
                fillProductForm(product);
            }
        }
        if (target.dataset.action === 'delete' && productId > 0) {
            deleteProduct(productId);
        }
    });
});
