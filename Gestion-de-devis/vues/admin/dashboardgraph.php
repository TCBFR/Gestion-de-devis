<div class="db-grid-3">
    <div class="db-card">
        <p class="db-card-title">Devis des 7 derniers jours</p>
        <?php if ($totalDevis > 0): ?>
            <div class="db-chart-wrap">
                <canvas id="chartDevis"></canvas>
            </div>
        <?php else: ?>
            <div class="db-empty">Aucun devis enregistré pour l'instant.</div>
        <?php endif; ?>
    </div>

    <div class="db-card">
        <p class="db-card-title">Produits les plus demandés</p>
        <?php if (!empty($top5)): ?>
            <ul class="db-prod-list">
                <?php foreach ($top5 as $nom => $count): ?>
                    <li class="db-prod-item">
                        <div class="db-prod-header">
                            <span class="db-prod-name" title="<?= htmlspecialchars($nom) ?>"><?= htmlspecialchars($nom) ?></span>
                            <span class="db-prod-count"><?= $count ?> devis</span>
                        </div>
                        <div class="db-prod-bar-bg">
                            <div class="db-prod-bar-fill" style="width:<?= round($count / $maxCount * 100) ?>%"></div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="db-empty">Aucune donnée produit disponible.</div>
        <?php endif; ?>
    </div>
</div>
