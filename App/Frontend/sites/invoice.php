<!DOCTYPE html>
<html lang="de">
<head>
    <?php
    $pageTitle = 'Rechnung - KickDistrict';
    include __DIR__ . '/components/head.php';
    ?>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom no-print">
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
    <div class="d-flex justify-content-between align-items-center no-print">
        <h1 class="h4 mb-3">Rechnung</h1>
        <button id="printInvoiceBtn" class="btn btn-outline-primary" type="button">Drucken</button>
    </div>

    <div id="invoiceMessage" class="hidden"></div>

    <div id="invoiceArea" class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h2 class="h5 mb-1">KickDistrict</h2>
                    <p class="mb-0">Danke für deine Bestellung!</p>
                </div>
                <div class="text-end">
                    <div id="invoiceNumber" class="fw-semibold"></div>
                    <div id="invoiceDate" class="text-muted"></div>
                </div>
            </div>

            <div class="mb-3">
                <div class="fw-semibold">Rechnungsadresse</div>
                <div id="invoiceCustomer"></div>
                <div id="invoiceEmail"></div>
            </div>

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
                    <tbody id="invoiceTableBody"></tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end">
                <strong>Gesamt: <span id="invoiceTotal">0.00 EUR</span></strong>
            </div>
        </div>
    </div>
</main>

<script src="../js/common.js"></script>
<script src="../js/auth.js"></script>
<script src="../js/invoice.js"></script>
</body>
</html>

