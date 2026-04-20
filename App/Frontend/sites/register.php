<!DOCTYPE html>
<html lang="de">
<head>
    <?php
    $pageTitle = 'Registrieren - KickDistrict';
    include __DIR__ . '/components/head.php';
    ?>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand" href="index.php">KickDistrict</a>
        <div class="navbar-nav ms-auto d-flex gap-2">
            <a class="nav-link" href="login.php">Login</a>
            <a class="nav-link" href="vouchers.php">Gutscheine</a>
        </div>
    </div>
</nav>

<main class="container">
    <div class="card page-card shadow-sm">
        <div class="card-body">
            <h1 class="h4 mb-3">Registrierung</h1>
            <form id="registerForm">
                <div class="mb-3">
                    <label class="form-label" for="salutation">Anrede</label>
                    <select class="form-select" id="salutation" name="salutation">
                        <option value="Herr">Herr</option>
                        <option value="Frau">Frau</option>
                        <option value="Divers">Divers</option>
                    </select>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="first_name">Vorname</label>
                        <input class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="last_name">Nachname</label>
                        <input class="form-control" id="last_name" name="last_name" required>
                    </div>
                </div>
                <div class="mt-3">
                    <label class="form-label" for="email">E-Mail</label>
                    <input class="form-control" id="email" name="email" type="email" required>
                </div>
                <div class="mt-3">
                    <label class="form-label" for="username">Benutzername</label>
                    <input class="form-control" id="username" name="username" minlength="4" required>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label" for="password">Passwort</label>
                        <input class="form-control" id="password" name="password" type="password" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="password_repeat">Passwort wiederholen</label>
                        <input class="form-control" id="password_repeat" name="password_repeat" type="password" required>
                    </div>
                </div>
                <button class="btn btn-primary mt-4" type="submit">Konto erstellen</button>
                <div id="registerMessage" class="hidden"></div>
            </form>
        </div>
    </div>
</main>

<script src="../js/common.js"></script>
<script src="../js/auth.js"></script>
</body>
</html>
