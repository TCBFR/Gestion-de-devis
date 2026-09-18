<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Devis <?= htmlspecialchars($devis['id']) ?></title>
    <style>
        <?php require __DIR__ . '/pdfstyles.php'; ?>
    </style>
</head>
<body>
<div class="header">
    <div>
        <?php if (!empty($vendeur['logo_url'])): ?>
            <img src="<?= htmlspecialchars($vendeur['logo_url']) ?>" alt="Logo" class="header-logo">
        <?php else: ?>
            <div class="header-title"><?= htmlspecialchars($vendeur['societe']) ?></div>
        <?php endif; ?>
    </div>
    <div class="header-meta">
        <p class="header-title" style="font-size:20px">DEVIS</p>
        <p>N° <?= htmlspecialchars($devis['id']) ?></p>
        <p>Date : <?= date('d/m/Y', strtotime($devis['created_at'])) ?></p>
        <p>Valable <?= (int) $config['validite_jours'] ?> jours</p>
    </div>
</div>

<div class="parties">
    <div class="partie">
        <div class="partie-label">Vendeur</div>
        <h3><?= htmlspecialchars($vendeur['societe']) ?></h3>
        <p><?= htmlspecialchars($vendeur['nom']) ?></p>
        <p><?= htmlspecialchars($vendeur['adresse']) ?></p>
        <p>SIRET : <?= htmlspecialchars($vendeur['siret']) ?></p>
        <p>📞 <a href="tel:<?= htmlspecialchars($vendeur['telephone']) ?>"><?= htmlspecialchars($vendeur['telephone']) ?></a></p>
        <p>✉ <a href="mailto:<?= htmlspecialchars($vendeur['email']) ?>"><?= htmlspecialchars($vendeur['email']) ?></a></p>
        <p>🌐 <a href="<?= htmlspecialchars($vendeur['site_web']) ?>"><?= htmlspecialchars($vendeur['site_web']) ?></a></p>
    </div>

    <div class="partie">
        <div class="partie-label">Client</div>
        <h3><?= htmlspecialchars($devis['client']['nom']) ?></h3>
        <p>📞 <a href="tel:<?= htmlspecialchars($devis['client']['telephone']) ?>"><?= htmlspecialchars($devis['client']['telephone']) ?></a></p>
        <p>✉ <a href="mailto:<?= htmlspecialchars($devis['client']['email']) ?>"><?= htmlspecialchars($devis['client']['email']) ?></a></p>
        <?php
        $adr = $devis['client']['adresse'] ?? [];
        if (!empty($adr['rue']) || !empty($adr['ville'])):
        ?>
            <p>
                <?= htmlspecialchars($adr['rue'] ?? '') ?><br>
                <?= htmlspecialchars($adr['cp'] ?? '') ?> <?= htmlspecialchars($adr['ville'] ?? '') ?>
            </p>
        <?php endif; ?>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Désignation</th>
            <th style="text-align:right">Prix TTC</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($devis['produits'] as $produit): ?>
            <?php $isLigneFix = in_array($produit['nom'], ['Livraison express fournisseur', "Main d'œuvre Réparation (1 h)"]); ?>
            <tr class="<?= $isLigneFix ? 'row-auto' : '' ?>">
                <td><?= htmlspecialchars($produit['nom']) ?></td>
                <td><?= number_format($produit['prix_client_ttc'], 2, ',', '') ?> €</td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td>Total TTC</td>
            <td><?= number_format($devis['total_ttc'], 2, ',', '') ?> €</td>
        </tr>
    </tfoot>
</table>

<div class="mentions">
    Devis valable <?= (int) $config['validite_jours'] ?> jours.
    Les prix sont indiqués en euros TTC.
    Contactez-nous pour confirmer votre demande ou obtenir plus d'informations.
</div>
</body>
</html>
