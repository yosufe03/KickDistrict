<!DOCTYPE html>
<html lang="de">
<head>
    <?php
    $pageTitle = 'Warenkorb - KickDistrict';
    include __DIR__ . '/components/head.php';
    ?>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand" href="index.php">KickDistrict</a>
        <div class="navbar-nav ms-auto d-flex gap-2"></div>
    </div>
</nav>

<main class="container mt-4">
    <h1 class="h4 mb-3">Warenkorb</h1>

    <div class="table-wrap">
        <table class="table align-middle">
            <thead>
            <tr>
                <th>Produkt</th>
                <th>Preis</th>
                <th>Menge</th>
                <th>Summe</th>
                <th></th>
            </tr>
            </thead>
            <tbody id="cartTableBody"></tbody>
        </table>
        <div class="d-flex justify-content-end align-items-center gap-3 mt-3">
            <strong>Gesamt: <span id="cartTotal">0.00 EUR</span></strong>
            <a href="checkout.php" class="btn btn-primary">Checkout</a>
            <a href="order_history.php" class="btn btn-outline-secondary">Bestellhistorie</a>
        </div>
        <div id="cartMessage" class="hidden"></div>
    </div>
</main>

<script src="../js/common.js"></script>
<script src="../js/auth.js"></script>
<script src="../js/cart.js"></script>
</body>
</html>
