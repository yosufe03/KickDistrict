<!DOCTYPE html>
<html lang="de">
<head>
    <?php
    $pageTitle = 'Produktdetails - KickDistrict';
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
    <div class="row g-4">
        <div class="col-md-5">
            <img id="productImage" class="img-fluid rounded shadow-sm" src="../res/img/product-placeholder.svg" alt="Produkt">
        </div>
        <div class="col-md-7">
            <h1 id="productName" class="h3">Produkt</h1>
            <p id="productPrice" class="h5 text-muted"></p>
            <p id="productDescription" class="mt-3"></p>
            <button id="addToCartBtn" class="btn btn-primary" type="button">In den Warenkorb</button>
            <div id="productDetailMessage" class="hidden"></div>
        </div>
    </div>
</main>

<script src="../js/common.js"></script>
<script src="../js/auth.js"></script>
<script src="../js/product-details.js"></script>
</body>
</html>

