<!DOCTYPE html>
<html lang="de">
<head>
    <?php
    $pageTitle = 'Gutscheine - KickDistrict';
    include __DIR__ . '/components/head.php';
    ?>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand" href="index.php">KickDistrict</a>
        <div class="navbar-nav ms-auto d-flex gap-2">
            <a class="nav-link" href="register.php" data-auth="out">Registrieren</a>
            <a class="nav-link" href="login.php" data-auth="out">Login</a>
            <a class="nav-link hidden" href="profile.php" data-auth="in">Profil</a>
            <button id="logoutBtn" data-auth="in" class="btn btn-outline-danger btn-sm hidden" type="button">Logout</button>
        </div>
    </div>
</nav>

<main class="container mt-4">
    <h1 class="h4 mb-3">Gutscheine</h1>
    <div class="table-wrap">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Code</th>
                <th>Wert</th>
                <th>Ablaufdatum</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody id="voucherTableBody"></tbody>
        </table>
        <div id="voucherMessage" class="hidden"></div>
    </div>
</main>

<script src="../js/common.js"></script>
<script src="../js/auth.js"></script>
<script src="../js/vouchers.js"></script>
</body>
</html>
