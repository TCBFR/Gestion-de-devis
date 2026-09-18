<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($vendeur['societe'] ?? 'BiscaPhone') ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/steps.css">
</head>
<body>

<header class="site-header">

    <div class="topbar">
        <div class="container topbar__inner">
            <span class="topbar__item">
                <span class="topbar__icon">📍</span>
                Biscarrosse – Landes – Bassin d'Arcachon
            </span>
            <span class="topbar__item topbar__item--phone">
                <span class="topbar__icon">📞</span>
                <strong>06 60 67 50 61</strong>
            </span>
            <span class="topbar__item">
                <span class="topbar__icon">🕐</span>
                Ouvert 7 jours sur 7 – 24h / 24
            </span>
        </div>
    </div>

    <nav class="navbar" aria-label="Navigation principale">
        <div class="container navbar__inner">

            <a href="/" class="navbar__brand" aria-label="Retour à l'accueil">
                <?php if (!empty($vendeur['logo_url'])): ?>
                    <img src="<?= htmlspecialchars($vendeur['logo_url']) ?>"
                         alt="Logo <?= htmlspecialchars($vendeur['societe']) ?>"
                         class="navbar__logo">
                <?php else: ?>
                    <span class="navbar__brand-text">
                        Bisca <strong>Phone</strong>
                    </span>
                <?php endif; ?>
            </a>

            <ul class="navbar__menu" role="list">
                <li><a href="#services"          class="navbar__link">Services</a></li>
                <li><a href="#tarifs"             class="navbar__link">Tarifs</a></li>
                <li><a href="#faq"                class="navbar__link">FAQ</a></li>
                <li><a href="#contact"            class="navbar__link">Contact</a></li>
                <li><a href="/admin/login"         class="navbar__link navbar__link--admin">Connexion admin</a></li>
                <li>
                    <a href="#catalogue-section" class="navbar__link navbar__link--devis">
                        Devis
                    </a>
                </li>
            </ul>

            <?php if (!empty($vendeur['telephone'])): ?>
                <a href="tel:<?= htmlspecialchars($vendeur['telephone']) ?>"
                   class="btn btn--call">
                    📞 Appeler
                </a>
            <?php endif; ?>

            <button class="navbar__burger" id="navbar-burger"
                    aria-controls="navbar-menu-mobile"
                    aria-expanded="false"
                    aria-label="Ouvrir le menu">
                <span></span><span></span><span></span>
            </button>

        </div>

        <div class="navbar__mobile" id="navbar-menu-mobile" hidden>
            <ul role="list">
                <li><a href="#services"          class="navbar__link">Services</a></li>
                <li><a href="#tarifs"             class="navbar__link">Tarifs</a></li>
                <li><a href="#faq"                class="navbar__link">FAQ</a></li>
                <li><a href="#contact"            class="navbar__link">Contact</a></li>
                <li><a href="/admin/login"         class="navbar__link">Connexion admin</a></li>
                <li><a href="#catalogue-section"  class="navbar__link">Devis</a></li>
            </ul>
        </div>
    </nav>

</header>

<?php require $viewFile; ?>

<script src="/assets/js/steps.js"></script>
</body>
</html>