<!DOCTYPE html>
<html lang="de">
<head>
    <?php
    $pageTitle = 'KickDistrict - Sprint 1';
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
            <button id="logoutBtn" data-auth="in" class="btn btn-outline-danger btn-sm hidden" type="button">Logout</button>
        </div>
    </div>
</nav>

<main class="container mt-4">
    <div class="p-4 bg-white rounded shadow-sm">
        <h1 class="h3">Sprint 1: Registrierung und Profil</h1>
        <p class="mb-1">Abgedeckt: Registrierung, Login, Logout, Profil bearbeiten, Gutscheine anzeigen.</p>
        <p class="text-muted mb-0">Nutze die Navigation, um die User Stories direkt zu testen.</p>
    </div>
</main>

<script src="../js/common.js"></script>
<script src="../js/auth.js"></script>
</body>
</html>
