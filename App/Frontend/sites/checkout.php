<!DOCTYPE html>
<html lang="de">
<head>
    <?php
    $pageTitle = 'Checkout - KickDistrict';
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
    <h1 class="h4 mb-3">Checkout</h1>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5 mb-3">Bestellübersicht</h2>
                    <div class="table-wrap">
                        <table class="table align-middle">
                            <thead>
                            <tr>
                                <th>Produkt</th>
                                <th>Preis</th>
                                <th>Menge</th>
                                <th>Summe</th>
                            </tr>
                            </thead>
                            <tbody id="checkoutTableBody"></tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between">
                        <strong>Gesamt</strong>
                        <strong id="checkoutTotal">0.00 EUR</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5 mb-3">Zahlung</h2>
                    <div class="mb-3">
                        <label class="form-label" for="paymentMethod">Zahlungsart</label>
                        <select class="form-select" id="paymentMethod">
                            <option value="card">Kreditkarte</option>
                            <option value="paypal">PayPal</option>
                            <option value="invoice">Rechnung</option>
                        </select>
                    </div>
                    <button id="placeOrderBtn" class="btn btn-primary w-100" type="button">Zahlungspflichtig bestellen</button>
                    <div id="checkoutMessage" class="hidden"></div>
                    <div id="checkoutActions" class="hidden mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="../js/common.js"></script>
<script src="../js/auth.js"></script>
<script src="../js/checkout.js"></script>
</body>
</html>

