<!DOCTYPE html>
<html lang="de">
<head>
    <?php
    $pageTitle = 'Admin Produkte - KickDistrict';
    include __DIR__ . '/components/head.php';
    ?>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand" href="index.php">KickDistrict</a>
        <div class="navbar-nav ms-auto d-flex gap-2">
            <a class="nav-link" href="vouchers.php">Gutscheine</a>
            <a class="nav-link" href="cart.php">Warenkorb</a>
            <button id="logoutBtn" class="btn btn-outline-danger btn-sm" type="button">Logout</button>
        </div>
    </div>
</nav>

<main class="container mt-4">
    <div class="row g-4">
        <section class="col-lg-4">
            <div class="admin-panel">
                <h1 class="h4 mb-3" id="productFormTitle">Produkt hinzufügen</h1>
                <form id="productForm">
                    <input type="hidden" id="productId" name="id">
                    <input type="hidden" id="productImagePath" name="image">
                    <div class="mb-3">
                        <label class="form-label" for="productName">Produktname</label>
                        <input class="form-control" id="productName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="productDescription">Beschreibung</label>
                        <textarea class="form-control" id="productDescription" name="description" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="productPrice">Preis</label>
                        <input class="form-control" id="productPrice" name="price" type="number" step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="adminCategorySelect">Kategorie</label>
                        <select class="form-select" id="adminCategorySelect" name="category_id" required></select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="productImage">Bild hochladen</label>
                        <input class="form-control" id="productImage" type="file" accept="image/*">
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" id="productSubmitBtn" type="submit">Produkt speichern</button>
                        <button class="btn btn-outline-secondary" id="productResetBtn" type="button">Zurücksetzen</button>
                    </div>
                    <div id="adminProductMessage" class="hidden"></div>
                </form>
            </div>
        </section>

        <section class="col-lg-8">
            <div class="admin-panel">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h2 class="h4 mb-0">Alle Produkte</h2>
                    <button class="btn btn-outline-secondary btn-sm" id="reloadProductsBtn" type="button">Aktualisieren</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                        <tr>
                            <th>Bild</th>
                            <th>Produkt</th>
                            <th>Preis</th>
                            <th>Kategorie</th>
                            <th>Aktionen</th>
                        </tr>
                        </thead>
                        <tbody id="adminProductTableBody"></tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/common.js"></script>
<script src="../js/auth.js"></script>
<script src="../js/admin_products.js"></script>
</body>
</html>
