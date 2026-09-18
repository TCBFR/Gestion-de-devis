<div class="db-grid-2">
    <div class="db-card">
        <p class="db-card-title">Derniers devis reçus</p>
        <?php if (!empty($derniers)): ?>
            <table class="db-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Total TTC</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($derniers as $d): ?>
                        <tr>
                            <td>
                                <div class="td-num"><?= htmlspecialchars($d['client']['nom']) ?></div>
                                <div class="td-muted"><?= htmlspecialchars($d['client']['email']) ?></div>
                            </td>
                            <td class="td-muted"><?= date('d/m/Y H:i', strtotime($d['created_at'])) ?></td>
                            <td class="td-num"><?= number_format($d['total_ttc'], 2, ',', ' ') ?> €</td>
                            <td><a href="/admin/devis/<?= urlencode($d['id']) ?>" class="td-link">Voir →</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="db-empty">Aucun devis enregistré pour l'instant.</div>
        <?php endif; ?>
    </div>
    <div class="db-card" id="password">
        <p class="db-card-title">Changer le mot de passe</p>
        <form method="POST" action="/admin/change-password">
            <div class="db-form-group">
                <label for="new_password">Nouveau mot de passe</label>
                <input type="password" id="new_password" name="new_password" minlength="8" required placeholder="8 caractères minimum">
            </div>
            <div class="db-form-group">
                <label for="confirm_password">Confirmer</label>
                <input type="password" id="confirm_password" name="confirm_password" minlength="8" required placeholder="Répétez le mot de passe">
            </div>
            <button type="submit" class="db-btn">Enregistrer</button>
        </form>
    </div>
</div>
