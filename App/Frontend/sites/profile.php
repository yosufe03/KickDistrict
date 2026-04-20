<!DOCTYPE html>
<html lang="de">
<head>
    <?php
    $pageTitle = 'Profil - KickDistrict';
    include __DIR__ . '/components/head.php';
    ?>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand" href="index.php">KickDistrict</a>
        <div class="navbar-nav ms-auto d-flex gap-2">
            <a class="nav-link" href="vouchers.php">Gutscheine</a>
            <button id="logoutBtn" class="btn btn-outline-danger btn-sm" type="button">Logout</button>
        </div>
    </div>
</nav>

<main class="container">
    <div class="card page-card shadow-sm">
        <div class="card-body">
            <h1 class="h4 mb-3">Profil bearbeiten</h1>
            <form id="profileForm">
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
                    <input class="form-control" id="username" name="username" disabled>
                </div>
                <div class="mt-3">
                    <label class="form-label" for="new_password">Neues Passwort (optional)</label>
                    <input class="form-control" id="new_password" name="new_password" type="password">
                </div>
                <div class="mt-3">
                    <label class="form-label" for="current_password">Aktuelles Passwort (Pflicht)</label>
                    <input class="form-control" id="current_password" name="current_password" type="password" required>
                </div>
                <button class="btn btn-primary mt-4" type="submit">Speichern</button>
                <div id="profileMessage" class="hidden"></div>
            </form>
        </div>
    </div>
</main>

<script src="../js/common.js"></script>
<script src="../js/auth.js"></script>
<script src="../js/profile.js"></script>
</body>
</html>
