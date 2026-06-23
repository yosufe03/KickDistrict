<!DOCTYPE html>
<html lang="de">
<head>
    <?php
    $pageTitle = 'Admin Kunden - KickDistrict';
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
        <h1 class="h4 mb-3">Kundenübersicht</h1>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>E-Mail</th>
                    <th>Rolle</th>
                    <th>Status</th>
                    <th>Aktionen</th>
                </tr>
                </thead>
                <tbody id="customerTableBody"></tbody>
            </table>
        </div>
        <div id="customerMessage" class="hidden"></div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/common.js"></script>
<script src="../js/auth.js"></script>
<script src="../js/admin_customers.js"></script>
</body>
</html>
