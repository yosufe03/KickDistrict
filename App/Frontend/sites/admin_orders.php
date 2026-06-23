<!DOCTYPE html>
<html lang="de">
<head>
    <?php
    $pageTitle = 'Admin Bestellungen - KickDistrict';
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
    <div class="admin-panel">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h1 class="h4 mb-0">Bestellübersicht</h1>
            <a class="btn btn-outline-secondary btn-sm" href="admin_customers.php">Kunden anzeigen</a>
        </div>
        <div id="ordersContainer" class="admin-orders"></div>
        <div id="orderMessage" class="hidden"></div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/common.js"></script>
<script src="../js/auth.js"></script>
<script src="../js/admin_orders.js"></script>
</body>
</html>
