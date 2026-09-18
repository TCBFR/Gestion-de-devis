<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiscaPhone — Administration</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div id="sidebar">
    <div class="sidebar-logo">
        <span class="brand">📱 BiscaPhone</span>
        <div class="sub">Administration</div>
    </div>

    <ul class="nav flex-column mt-1 px-0 flex-grow-1">
        <div class="nav-section">Navigation</div>
        <li class="nav-item">
            <a href="/admin" class="nav-link <?= ($viewFile ?? '') === VIEWS_DIR.'/admin/dashboard.php' ? 'active' : '' ?>">
                <span class="nav-icon">🗂</span> Tableau de bord
            </a>
        </li>

        <div class="nav-section">Données</div>
        <li class="nav-item">
            <a href="/admin/import" class="nav-link <?= str_contains($viewFile ?? '', 'import') ? 'active' : '' ?>">
                <span class="nav-icon">📤</span> Importer CSV
            </a>
        </li>
        <li class="nav-item">
            <a href="/admin/export-csv" class="nav-link">
                <span class="nav-icon">📥</span> Exporter BD
            </a>
        </li>
        <li class="nav-item">
            <a href="/admin/backup" class="nav-link">
                <span class="nav-icon">💾</span> Backup .sql
            </a>
        </li>

        <div class="nav-section">Devis</div>
        <li class="nav-item">
            <a href="/admin/devis" class="nav-link <?= str_contains($viewFile ?? '', 'devis') ? 'active' : '' ?>">
                <span class="nav-icon">🧾</span> Devis générés
            </a>
        </li>
        <li class="nav-item">
            <a href="/admin/devis-export-csv" class="nav-link">
                <span class="nav-icon">📊</span> Exporter devis CSV
            </a>
        </li>

        <div class="nav-section">Compte</div>
        <li class="nav-item">
            <a href="/admin#password" class="nav-link">
                <span class="nav-icon">🔑</span> Mot de passe
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a href="/admin/logout">⬅ Déconnexion</a>
    </div>
</div>

<div id="main">
    <?php require $viewFile; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
