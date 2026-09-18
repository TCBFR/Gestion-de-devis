<div class="db-header">
    <div>
        <h1>Tableau de bord</h1>
        <p class="date"><?= date('l j F Y') ?></p>
    </div>
    <span class="db-online">● En ligne</span>
</div>

<?php if (($_GET['success'] ?? '') === 'password'): ?>
    <div class="db-alert-success">✓ Mot de passe mis à jour avec succès.</div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="db-alert-danger">! <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<p class="db-section">Vue d'ensemble</p>
<div class="db-stats">
    <div class="db-stat">
        <a href="/admin">
            <div class="db-stat-num"><?= $totalDevis ?></div>
            <div class="db-stat-label">Devis total</div>
            <div class="db-stat-sub">↗ Voir tous les devis</div>
        </a>
    </div>

    <div class="db-stat">
        <div class="db-stat-num"><?= $countMois ?></div>
        <div class="db-stat-label">Ce mois-ci</div>
        <div class="db-stat-sub <?= $countMois === 0 ? 'muted' : '' ?>">
            <?= $countMois > 0 ? "dont $countAujourdhui aujourd'hui" : 'Aucun encore' ?>
        </div>
    </div>

    <div class="db-stat">
        <div class="db-stat-num"><?= number_format($caTotal, 0, ',', ' ') ?> €</div>
        <div class="db-stat-label">CA total TTC</div>
        <div class="db-stat-sub <?= $caTotal === 0.0 ? 'muted' : '' ?>">
            <?= $caTotal > 0 ? 'Tous devis confondus' : 'Aucun devis' ?>
        </div>
    </div>

    <div class="db-stat">
        <div class="db-stat-num"><?= number_format($panierMoyen, 0, ',', ' ') ?> €</div>
        <div class="db-stat-label">Panier moyen TTC</div>
        <div class="db-stat-sub <?= $panierMoyen === 0.0 ? 'muted' : '' ?>">
            <?= $panierMoyen > 0 ? 'Par devis' : 'Aucun devis' ?>
        </div>
    </div>
</div>
