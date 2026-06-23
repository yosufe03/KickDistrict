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
            <a class="nav-link" href="cart.php">Warenkorb</a>
            <button id="logoutBtn" data-auth="in" class="btn btn-outline-danger btn-sm hidden" type="button">Logout</button>
        </div>
    </div>
</nav>

<main class="container mt-4">
    <h1 class="h4 mb-3">Gutscheine</h1>
    <div class="row g-4">
        <section class="col-lg-4 hidden" id="voucherAdminPanel">
            <div class="admin-panel">
                <h2 class="h5 mb-3">Gutschein erstellen</h2>
                <form id="voucherCreateForm">
                    <div class="mb-3">
                        <label class="form-label" for="voucherCode">Code</label>
                        <div class="input-group">
                            <input class="form-control" id="voucherCode" name="code" maxlength="20" placeholder="Automatisch">
                            <button class="btn btn-outline-secondary" id="generateVoucherBtn" type="button">Generieren</button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="voucherValue">Wert</label>
                        <input class="form-control" id="voucherValue" name="value" type="number" step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="voucherExpiry">Ablaufdatum</label>
                        <input class="form-control" id="voucherExpiry" name="expiry_date" type="date" required>
                    </div>
                    <button class="btn btn-primary" type="submit">Erstellen</button>
                    <div id="voucherCreateMessage" class="hidden"></div>
                </form>
            </div>
        </section>

        <section class="col-lg-8" id="voucherListPanel">
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
        </section>
    </div>
</main>

<script src="../js/common.js"></script>
<script src="../js/auth.js"></script>
<script src="../js/vouchers.js"></script>
<script src="../js/voucher_admin.js"></script>
</body>
</html>
