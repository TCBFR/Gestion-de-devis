<div class="d-flex flex-column flex-md-row
                    justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h3 id="catalogue-title" class="catalogue-title"></h3>

            </div>
            <button id="btn-panier-header"
                    class="btn btn-primary d-none"
                    aria-label="Voir le panier">
                Voir le panier
                <span id="btn-panier-count-header"
                      class="badge bg-white text-dark ms-2"
                      aria-live="polite">0</span>
            </button>
        </div>



        <div id="catalogue-empty"
             class="alert alert-warning d-none"
             role="alert">
            Aucune pièce disponible pour ce modèle pour le moment.
        </div>

        <div id="repair-summary-panel" class="card shadow-sm rounded-4 p-4 mt-3 d-none" style="max-width: 960px; margin: 0 auto 1.5rem; border: 1px solid var(--color-border); background: #fff;">
            <h4 class="h5 mb-2" style="color: var(--color-dark);">Résumé des réparations</h4>
            <p class="text-muted mb-3" style="margin-bottom: 0.75rem;">Voici les réparations sélectionnées pour la suite du devis.</p>
            <ul id="repair-summary-list" class="list-unstyled d-flex flex-wrap gap-2 mb-0"></ul>
        </div>

        <div id="repair-product-examples" class="d-none" style="max-width: 960px; margin: 0 auto 1.5rem;">
            <div id="product-valider-wrap" style="display:none; text-align:center; margin-bottom:1.5rem;">
                <button id="btn-valider-produits" style="background:var(--color-accent); color:#fff; border:none; padding:0.85rem 2.5rem; border-radius:14px; font-weight:700; font-size:1.05rem; cursor:pointer; font-family:var(--font-body); box-shadow:0 4px 18px rgba(0,0,100,0.13); transition:opacity 0.18s;">
                    Valider ma réparation →
                </button>
            </div>



            <div id="product-loader" class="text-center py-4 d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement des produits…</span>
                </div>
                <p class="text-muted mt-2">Chargement des produits disponibles…</p>
            </div>

            <div id="product-empty" class="alert alert-warning d-none" role="alert">
                Aucun produit trouvé pour cette réparation et ce modèle.
            </div>

            <div class="device-grid repair-grid" id="product-cards-grid" role="list" aria-label="Produits disponibles">
                <!-- Produits chargés dynamiquement via chargerProduits() -->
            </div>
        </div>