<!-- views/admin/devis_list.php
     Liste tous les devis générés avec accès au détail et export CSV. -->

<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="admin-page-title mb-0">Devis générés</h1>
    <a href="/admin/devis-export-csv" class="btn btn-outline-dark btn-sm">
        📥 Exporter tout en CSV
    </a>
</div>

<?php if (empty($devisList)): ?>
    <?php if (!empty($error)): ?>
        <p class="text-danger"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <p class="text-muted">Aucun devis généré pour le moment.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Date</th>
                    <th>Client</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th class="text-end">Total TTC</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($devisList as $d): ?>
                    <tr>
                        <td class="text-muted" style="font-size:12px">
                            <?= date('d/m/Y H:i', strtotime($d['created_at'])) ?>
                        </td>
                        <td><?= htmlspecialchars($d['client']['nom']) ?></td>
                        <td>
                            <a href="mailto:<?= htmlspecialchars($d['client']['email']) ?>">
                                <?= htmlspecialchars($d['client']['email']) ?>
                            </a>
                        </td>
                        <td>
                            <a href="tel:<?= htmlspecialchars($d['client']['telephone']) ?>">
                                <?= htmlspecialchars($d['client']['telephone']) ?>
                            </a>
                        </td>
                        <td class="text-end fw-bold">
                            <?= number_format($d['total_ttc'], 2, ',', '') ?> €
                        </td>
                        <td class="text-end">
                            <div style="display:flex; gap:0.5rem; justify-content:flex-end;">
                                <a href="/devis/<?= htmlspecialchars($d['id']) ?>"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-dark">
                                    Voir PDF
                                </a>
                                <a href="/admin/devis/<?= htmlspecialchars($d['id']) ?>/export-csv"
                                   class="btn btn-sm btn-outline-secondary">
                                    Export CSV
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>