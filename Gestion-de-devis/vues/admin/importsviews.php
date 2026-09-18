<?php
/**
 * views/admin/importsviews.php
 * Formulaire d'import CSV fournisseur.
 */
?>
<link rel="stylesheet" href="/assets/css/import.css">

<div class="imp-header">
    <h1>Import des tarifs fournisseur</h1>
    <p>Mettez à jour le catalogue produits depuis un fichier CSV.</p>
</div>

<div class="imp-card">
    <p class="imp-card-title">Choisir un fichier CSV</p>
    <form method="POST" action="/admin/import" enctype="multipart/form-data">
        <div class="imp-dropzone" id="dropzone">
            <input type="file" name="csv" id="csv" accept=".csv,text/csv" required>
            <div class="imp-dropzone-icon">📤</div>
            <div class="imp-dropzone-label">Glissez un fichier ou cliquez pour parcourir</div>
            <div class="imp-dropzone-hint">Fichiers .csv uniquement</div>
            <div class="imp-dropzone-file" id="selectedFile"></div>
        </div>
        <div style="display:flex; gap:0.5rem; margin-top:0.75rem;">
            <button type="submit" class="imp-btn" id="submitBtn" disabled>
                Importer et mettre à jour les tarifs
            </button>
            <button type="submit" name="save_server" value="1" class="imp-btn" id="uploadBtn" disabled style="background:#7b61ff;">
                Téléverser dans storage/imports/
            </button>
        </div>
    </form>
    <p class="imp-info" style="margin-top:1rem;">Une sauvegarde automatique de la base est créée avant chaque import.</p>
</div>

<div class="imp-card">
    <p class="imp-card-title">Importer depuis un fichier serveur</p>
    <p class="imp-dropzone-hint">Si le fichier est trop volumineux pour l'upload, placez-le dans <code>storage/imports/</code>.</p>

    <?php if (!empty($importFiles)): ?>
        <form method="POST" action="/admin/import">
            <div class="form-group" style="margin-bottom:1rem;">
                <label for="existing_file">Fichier CSV disponible</label>
                <select name="existing_file" id="existing_file" class="form-select" style="width:100%; padding:0.75rem; border:1px solid #d1d5db; border-radius:0.75rem;">
                    <option value="">-- Choisir un fichier --</option>
                    <?php foreach ($importFiles as $serverCsv): ?>
                        <option value="<?= htmlspecialchars($serverCsv) ?>"><?= htmlspecialchars($serverCsv) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="imp-btn">Importer ce fichier serveur</button>
        </form>
    <?php else: ?>
        <p class="imp-list-title">Aucun fichier CSV trouvé dans <code>storage/imports/</code>.</p>
    <?php endif; ?>
</div>

<?php if ($rapport !== null): ?>
<div class="imp-card">
    <p class="imp-card-title">Résultat de l'import</p>

    <?php if (!empty($rapport['error'])): ?>
        <div class="imp-alert imp-alert-err">
            ✕ <?= htmlspecialchars($rapport['error']) ?>
        </div>
    <?php else: ?>
        <div class="imp-alert imp-alert-ok">
            ✓ <strong><?= (int) $rapport['imported'] ?> produit(s)</strong> importé(s) ou mis à jour avec succès.
        </div>

        <?php if (!empty($rapport['warnings'])): ?>
            <p class="imp-list-title">⚠ <?= count($rapport['warnings']) ?> ligne(s) ignorée(s)</p>
            <ul class="imp-list warn">
                <?php foreach ($rapport['warnings'] as $w): ?>
                    <li><?= htmlspecialchars($w) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($rapport['errors'])): ?>
            <p class="imp-list-title">✕ <?= count($rapport['errors']) ?> erreur(s)</p>
            <ul class="imp-list err">
                <?php foreach ($rapport['errors'] as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php endif; ?>

<script src="/assets/js/import.js"></script>
