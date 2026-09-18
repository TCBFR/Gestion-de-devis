</div><div id="panel-modele" class="d-none mt-4">
            <div style="max-width: 600px; margin: 2rem auto 0; background: #ffffff; padding: 2.5rem; border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid var(--color-border);">
                
                <div style="margin-bottom: 1rem;">
                    <span id="badge-type-selectionne" style="display: inline-block; background: var(--color-light); color: var(--color-accent); border: 1px solid var(--color-border); padding: 0.5rem 1.2rem; border-radius: 50px; font-weight: 700; font-size: 0.95rem;"></span>
                </div>

                <h2 class="h5 mb-4 text-muted" style="font-weight: 600; font-size: 1.1rem; margin-bottom: 1.5rem;">Sélectionnez votre marque et votre modèle</h2>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; text-align: left;">

                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label for="sel-marque" class="form-label" style="font-weight: 600; font-size: 0.9rem; color: var(--color-dark);">
                            Marque
                        </label>
                        <select id="sel-marque" style="width: 100%; border: 1px solid var(--color-border); border-radius: 12px; padding: 0.85rem 1rem; font-size: 1rem; font-family: var(--font-body); color: var(--color-dark); background-color: #fff; cursor: pointer;">
                            <option value="">-- Choisir la marque --</option>
                        </select>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label for="sel-modele" class="form-label" style="font-weight: 600; font-size: 0.9rem; color: var(--color-dark);">
                            Modèle
                        </label>
                        <select id="sel-modele" style="width: 100%; border: 1px solid var(--color-border); border-radius: 12px; padding: 0.85rem 1rem; font-size: 1rem; font-family: var(--font-body); color: var(--color-dark); background-color: #fff; cursor: pointer;" disabled>
                            <option value="">-- Choisir le modèle --</option>
                        </select>
                    </div>

                </div>

                <div id="modele-loader" class="d-none" style="margin: 1rem 0; color: var(--color-muted); font-size: 0.9rem;">
                    <span style="display: inline-block; animation: spin 1s linear infinite; margin-right: 0.5rem;">⏳</span>
                    <span>Chargement des données…</span>
                </div>



            </div>
        </div>