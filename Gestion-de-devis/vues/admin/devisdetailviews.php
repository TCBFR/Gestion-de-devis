<?php
// Vue minimale pour afficher le détail d'un devis
/** @var array $devis */
?>
<h2>Détails du devis <?= htmlspecialchars($devis['id'] ?? '') ?></h2>
<p>Date : <?= htmlspecialchars($devis['created_at'] ?? '') ?></p>
<h3>Client</h3>
<p><?= htmlspecialchars($devis['client']['nom'] ?? '') ?> — <?= htmlspecialchars($devis['client']['email'] ?? '') ?></p>
<h3>Produits</h3>
<ul>
<?php foreach ($devis['produits'] ?? [] as $p): ?>
    <li><?= htmlspecialchars($p['nom'] ?? '') ?> — <?= htmlspecialchars(number_format($p['prix_client_ttc'] ?? 0, 2, ',', '')) ?> €</li>
<?php endforeach; ?>
</ul>
