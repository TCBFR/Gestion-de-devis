<!-- ── Panel Panier (step 5) ── -->
        <div id="panel-panier" class="d-none" style="max-width:960px; margin:0 auto; padding-bottom:2rem;">

            <h2 style="font-size:1.3rem; font-weight:700; color:var(--color-dark); margin-bottom:1.5rem; text-align:center;">
                Votre panier
            </h2>

            <div id="panier-liste" style="display:flex; flex-direction:column; gap:1rem; margin-bottom:1.5rem;"></div>

            <div id="panier-vide" class="d-none" style="text-align:center; padding:2rem; color:var(--color-muted); font-size:1rem; background:#fff; border-radius:14px; border:1px solid var(--color-border);">
                Votre panier est vide.
            </div>

            <div style="border-top:2px solid var(--color-border); padding-top:1.25rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
                <span style="font-size:1.15rem; font-weight:700; color:var(--color-dark);">Total</span>
                <span id="panier-total" style="font-size:1.4rem; font-weight:800; color:var(--color-accent);">0 €</span>
            </div>

            <div style="text-align:center; margin-top:1.75rem;">
                <button id="btn-valider-panier"
                        style="background:var(--color-accent); color:#fff; border:none; padding:0.85rem 2.5rem; border-radius:14px; font-weight:700; font-size:1.05rem; cursor:pointer; font-family:var(--font-body); box-shadow:0 4px 18px rgba(0,0,100,0.13); transition:opacity 0.18s;">
                    Valider mon panier →
                </button>
            </div>

        </div>