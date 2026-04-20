<!DOCTYPE html>
<html lang="de">
<head>
    <?php
    $pageTitle = 'Login - KickDistrict';
    include __DIR__ . '/components/head.php';
    ?>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand" href="index.php">KickDistrict</a>
        <div class="navbar-nav ms-auto d-flex gap-2">
            <a class="nav-link" href="register.php">Registrieren</a>
            <a class="nav-link" href="vouchers.php">Gutscheine</a>
        </div>
    </div>
</nav>

<main class="container">
    <div class="card page-card shadow-sm">
        <div class="card-body">
            <h1 class="h4 mb-3">Login</h1>
            <form id="loginForm">
                <div class="mb-3">
                    <label class="form-label" for="username_email">Benutzername oder E-Mail</label>
                    <input class="form-control" id="username_email" name="username_email" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Passwort</label>
                    <input class="form-control" id="password" name="password" type="password" required>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" id="remember_me" name="remember_me" type="checkbox">
                    <label class="form-check-label" for="remember_me">Login merken</label>
                </div>
                <button class="btn btn-primary" type="submit">Einloggen</button>
                <div id="loginMessage" class="hidden"></div>
            </form>
        </div>
    </div>
</main>

<script src="../js/common.js"></script>
<script src="../js/auth.js"></script>
</body>
</html>
