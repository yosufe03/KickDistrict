<!DOCTYPE html>
<html lang="de">
<head>
    <?php
    $pageTitle = 'Bestellhistorie - KickDistrict';
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
    <h1 class="h4 mb-3">Bestellhistorie</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-wrap">
                <table class="table align-middle">
                    <thead>
                    <tr>
                        <th>Bestellung</th>
                        <th>Datum</th>
                        <th>Status</th>
                        <th>Produkte</th>
                        <th>Summe</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="orderTableBody"></tbody>
                </table>
            </div>
            <div id="orderMessage" class="hidden"></div>
        </div>
    </div>
</main>

<script src="../js/common.js"></script>
<script src="../js/auth.js"></script>
<script src="../js/order_history.js"></script>
</body>
</html>

