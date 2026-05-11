<!DOCTYPE html>
<html lang="de">
<head>
    <?php
    $pageTitle = 'KickDistrict';
    include __DIR__ . '/components/head.php';
    ?>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand" href="index.php">KickDistrict</a>
        <div class="navbar-nav ms-auto d-flex gap-2">
            <a class="nav-link" data-auth="out" href="register.php">Registrieren</a>
            <a class="nav-link" data-auth="out" href="login.php">Login</a>
            <a class="nav-link hidden" data-auth="in" href="profile.php">Profil</a>
            <a class="nav-link" href="vouchers.php">Gutscheine</a>
            <a class="nav-link" href="cart.php">Warenkorb</a>
            <button id="logoutBtn" data-auth="in" class="btn btn-outline-danger btn-sm hidden" type="button">Logout</button>
        </div>
    </div>
</nav>

<main class="container mt-4">
    <div class="p-4 bg-white rounded shadow-sm mb-4">
        <h1 class="h3">Produktsuche</h1>
        <p class="mb-0">Produkte suchen, Details ansehen und in den Warenkorb legen.</p>
    </div>

    <form id="searchForm" class="row g-2 align-items-end mb-3">
        <div class="col-md-6">
            <label class="form-label" for="searchInput">Suche</label>
            <input class="form-control" id="searchInput" type="text" placeholder="Produktname oder Stichwort">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="categorySelect">Kategorie</label>
            <select class="form-select" id="categorySelect">
                <option value="">Alle Kategorien</option>
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button class="btn btn-primary" type="submit">Filtern</button>
        </div>
    </form>

    <div id="productMessage" class="hidden"></div>
    <div id="productGrid" class="row g-3 product-grid"></div>
</main>

<script src="../js/common.js"></script>
<script src="../js/auth.js"></script>
<script src="../js/products.js"></script>
</body>
</html>
