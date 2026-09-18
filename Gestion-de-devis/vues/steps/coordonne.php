<!-- ── Panel Coordonnées (step 6) ── -->
        <div id="panel-coordonnees" class="d-none" style="max-width:600px; margin:0 auto; padding-bottom:2rem;">

            <h2 style="font-size:1.3rem; font-weight:700; color:var(--color-dark); margin-bottom:0.4rem; text-align:center;">
                Vos coordonnées
            </h2>
            <p style="text-align:center; color:var(--color-muted); font-size:0.95rem; margin-bottom:1.75rem;">
                Renseignez vos informations pour recevoir votre devis.
            </p>

            <div style="background:#fff; border:1px solid var(--color-border); border-radius:18px; padding:2rem; box-shadow:var(--shadow); display:flex; flex-direction:column; gap:1.25rem;">

                <!-- Nom -->
                <div class="coord-field">
                    <label for="coord-nom" class="coord-label">Nom complet <span style="color:#e74c3c;">*</span></label>
                    <input id="coord-nom" type="text" class="coord-input" placeholder="Jean Dupont" autocomplete="name" />
                    <span class="coord-error" id="err-nom"></span>
                </div>

                <!-- Email -->
                <div class="coord-field">
                    <label for="coord-email" class="coord-label">Adresse e-mail <span style="color:#e74c3c;">*</span></label>
                    <input id="coord-email" type="email" class="coord-input" placeholder="jean@exemple.fr" autocomplete="email" />
                    <span class="coord-error" id="err-email"></span>
                </div>

                <!-- Téléphone -->
                <div class="coord-field">
                    <label for="coord-tel" class="coord-label">Numéro de téléphone <span style="color:#e74c3c;">*</span></label>
                    <input id="coord-tel" type="tel" class="coord-input" placeholder="06 12 34 56 78" autocomplete="tel" />
                    <span class="coord-error" id="err-tel"></span>
                </div>

                <!-- Code postal -->
                <div class="coord-field">
                    <label for="coord-cp" class="coord-label">Code postal <span style="color:#e74c3c;">*</span></label>
                    <input id="coord-cp" type="text" class="coord-input" placeholder="40600" maxlength="5" autocomplete="postal-code" />
                    <span class="coord-error" id="err-cp"></span>
                </div>

                <!-- Adresse postale -->
                <div class="coord-field">
                    <label for="coord-adresse" class="coord-label">Adresse postale <span style="color:#e74c3c;">*</span></label>
                    <input id="coord-adresse" type="text" class="coord-input" placeholder="12 rue de la Plage, Biscarrosse" autocomplete="street-address" />
                    <span class="coord-error" id="err-adresse"></span>
                </div>

                <!-- Bouton -->
                <div style="text-align:center; margin-top:0.5rem;">
                    <button id="btn-valider-coordonnees"
                            style="background:var(--color-accent); color:#fff; border:none; padding:0.85rem 2.5rem; border-radius:14px; font-weight:700; font-size:1.05rem; cursor:pointer; font-family:var(--font-body); box-shadow:0 4px 18px rgba(0,0,100,0.13); transition:opacity 0.18s; width:100%;">
                        Recevoir mon devis →
                    </button>
                </div>

            </div>
        </div>

    </div>