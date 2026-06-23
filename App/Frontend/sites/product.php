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
            <img id="productImage" class="img-fluid rounded shadow-sm" src="data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22640%22 height=%22480%22 viewBox=%220 0 640 480%22%3E%3Crect width=%22640%22 height=%22480%22 fill=%22%23eef1f5%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-family=%22Arial, sans-serif%22 font-size=%2232%22 fill=%227a8794%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22%3EKickDistrict%3C/text%3E%3C/svg%3E" alt="Produkt">
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

<script src="../js/common.js?v=2"></script>
<script src="../js/auth.js"></script>
<script src="../js/product-details.js?v=2"></script>
</body>
</html>
