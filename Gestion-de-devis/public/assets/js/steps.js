/* ----------------------------------------------------------------
   Menu burger (mobile)
   Bascule l'attribut hidden du menu mobile et aria-expanded.
---------------------------------------------------------------- */
(function () {
    const burger = document.getElementById('navbar-burger');
    const menu   = document.getElementById('navbar-menu-mobile');

    if (!burger || !menu) return;

    burger.addEventListener('click', () => {
        const isOpen = !menu.hidden;
        menu.hidden            = isOpen;           // bascule visibilité
        burger.setAttribute('aria-expanded', String(!isOpen));
        burger.classList.toggle('is-active', !isOpen);
    });

    /* Fermer le menu si on clique sur un lien interne */
    menu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            menu.hidden = true;
            burger.setAttribute('aria-expanded', 'false');
            burger.classList.remove('is-active');
        });
    });
})();



/* ================================================================
   STEPPER MULTI-ÉTAPES
   Étape 1 : clic card → affiche panel-modele (step 2)
   Étape 2 : sélection marque → charge modèles, sélection modèle
             → active bouton "Voir les réparations"
             → clic bouton → charge le catalogue + scroll
   ================================================================ */
(function () {

    /* ── Éléments DOM ─────────────────────────────────────────── */
    const cards         = document.querySelectorAll('#panel-appareil .device-card');
    const panelAppareil = document.getElementById('panel-appareil');
    const panelModele   = document.getElementById('panel-modele');
    const badgeType     = document.getElementById('badge-type-selectionne');
    const selMarque     = document.getElementById('sel-marque');
    const selModele     = document.getElementById('sel-modele');

    const modeleLoader  = document.getElementById('modele-loader');

    /* Éléments du stepper */
    const stepAppareil    = document.getElementById('step-appareil');
    const stepModele      = document.getElementById('step-modele');
    const stepProbleme    = document.getElementById('step-probleme');
    const stepReparations = document.getElementById('step-reparations');
    const stepPanier      = document.getElementById('step-panier');
    const stepCoordonnees = document.getElementById('step-coordonnees');

    let typeSelectionne = '';
    let marqueSelectionnee = '';
    let modeleSelectionne  = '';
    let currentStep = 1;

    /* Labels lisibles pour le badge */
    const typeLabels = {
        android  : '📱 Android',
        iphone   : '🍎 iPhone',
        tablet   : '📟 Tablette',
        computer : '💻 Ordinateur'
    };

    /* Mapping : types frontend → types BD pour filtrage des marques/modèles */
    const typeAppareilMap = {
        android  : 'Téléphonie',
        iphone   : 'Téléphonie',
        tablet   : 'Tablette',
        computer : 'Ordinateur'
    };

    /* Mapping : types frontend → marques spécifiques à afficher */
    const typeMarqueMap = {
        android  : ['Samsung', 'Xiaomi', 'Motorola', 'Realme', 'Vivo', 'OnePlus', 'OPPO', 'Honor', 'HTC', 'Huawei', 'ZTE', 'Nothing', 'Wiko', 'Google', 'BlackBerry', 'HMD', 'Nokia', 'TCL', 'Asus', 'Lenovo', 'Alcatel', 'Crosscall', 'Blackview', 'Caterpillar', 'Moto'],
        iphone   : ['Apple'],
        tablet   : ['Apple', 'Samsung', 'Lenovo', 'Asus', 'Sony', 'Microsoft', 'Huawei', 'Xiaomi'],
        computer : ['Apple', 'Dell', 'Lenovo', 'Asus', 'HP', 'Microsoft', 'Sony', 'LG']
    };

    function montrerVuePourStep(etape) {
        const catalogueSection      = document.getElementById('catalogue-section');
        const repairGrid            = document.getElementById('repair-cards-grid');
        const repairProductExamples = document.getElementById('repair-product-examples');
        const repairValiderWrap     = document.getElementById('repair-valider-wrap');
        const panelPanier           = document.getElementById('panel-panier');
        const panelCoordonnees      = document.getElementById('panel-coordonnees');

        /* Tout masquer d'abord */
        if (repairGrid)            repairGrid.classList.add('d-none');
        if (repairProductExamples) repairProductExamples.classList.add('d-none');
        if (repairValiderWrap)     repairValiderWrap.style.display = 'none';
        if (panelPanier)           panelPanier.classList.add('d-none');
        if (panelCoordonnees)      panelCoordonnees.classList.add('d-none');

        if (etape <= 1) {
            panelAppareil.classList.remove('d-none');
            panelModele.classList.add('d-none');
            if (catalogueSection) catalogueSection.classList.add('d-none');
            return;
        }
        if (etape === 2) {
            panelAppareil.classList.add('d-none');
            panelModele.classList.remove('d-none');
            if (catalogueSection) catalogueSection.classList.add('d-none');
            return;
        }

        panelAppareil.classList.add('d-none');
        panelModele.classList.add('d-none');
        if (catalogueSection) catalogueSection.classList.remove('d-none');

        if (etape === 3) {
            if (repairGrid) repairGrid.classList.remove('d-none');
            return;
        }
        if (etape === 4) {
            if (repairProductExamples) repairProductExamples.classList.remove('d-none');
            return;
        }
        if (etape === 5) {
            if (panelPanier) {
                panelPanier.classList.remove('d-none');
                if (typeof window.remplirPanier === 'function') window.remplirPanier();
            }
            return;
        }
        if (etape === 6) {
            if (panelCoordonnees) panelCoordonnees.classList.remove('d-none');
            return;
        }
    }

    /* ── Utilitaire : mise à jour du stepper ──────────────────── */
    function activerStep(etape) {
        // Réinitialiser
        [stepAppareil, stepModele, stepProbleme, stepReparations, stepPanier, stepCoordonnees].forEach(s => {
            if (!s) return;
            s.classList.remove('step--active', 'step--done');
            s.removeAttribute('aria-current');
        });

        if (etape === 1) {
            stepAppareil.classList.add('step--active');
            stepAppareil.setAttribute('aria-current', 'step');
        } else if (etape === 2) {
            stepAppareil.classList.add('step--done');
            stepModele.classList.add('step--active');
            stepModele.setAttribute('aria-current', 'step');
        } else if (etape === 3) {
            stepAppareil.classList.add('step--done');
            stepModele.classList.add('step--done');
            if (stepProbleme) {
                stepProbleme.classList.add('step--active');
                stepProbleme.setAttribute('aria-current', 'step');
            }
        } else if (etape === 4) {
            stepAppareil.classList.add('step--done');
            stepModele.classList.add('step--done');
            stepProbleme.classList.add('step--done');
            if (stepReparations) {
                stepReparations.classList.add('step--active');
                stepReparations.setAttribute('aria-current', 'step');
            }
        } else if (etape === 5) {
            stepAppareil.classList.add('step--done');
            stepModele.classList.add('step--done');
            stepProbleme.classList.add('step--done');
            stepReparations.classList.add('step--done');
            if (stepPanier) {
                stepPanier.classList.add('step--active');
                stepPanier.setAttribute('aria-current', 'step');
            }
        } else if (etape === 6) {
            stepAppareil.classList.add('step--done');
            stepModele.classList.add('step--done');
            stepProbleme.classList.add('step--done');
            stepReparations.classList.add('step--done');
            stepPanier.classList.add('step--done');
            if (stepCoordonnees) {
                stepCoordonnees.classList.add('step--active');
                stepCoordonnees.setAttribute('aria-current', 'step');
            }
        }

        currentStep = etape;
        montrerVuePourStep(etape);
    }

    document.querySelectorAll('.step[data-step]').forEach(stepItem => {
        stepItem.addEventListener('click', () => {
            const targetStep = Number(stepItem.dataset.step);
            if (!Number.isFinite(targetStep) || targetStep >= currentStep || targetStep < 1) return;
            activerStep(targetStep);
        });

        stepItem.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                stepItem.click();
            }
        });
    });

    /* ── STEP 1 → STEP 2 : clic sur une card ─────────────────── */
    cards.forEach(card => {
        card.addEventListener('click', () => {

            /* Highlight la carte */
            cards.forEach(c => {
                c.classList.remove('device-card--selected');
                c.setAttribute('aria-pressed', 'false');
            });
            card.classList.add('device-card--selected');
            card.setAttribute('aria-pressed', 'true');

            typeSelectionne = card.dataset.type;

            /* Mettre à jour le badge */
            badgeType.textContent = typeLabels[typeSelectionne] || typeSelectionne;

            /* Passer au panel modèle */
            panelAppareil.classList.add('d-none');
            panelModele.classList.remove('d-none');
            activerStep(2);

            /* Réinitialiser les selects */
            selMarque.innerHTML = '<option value="">-- Choisir la marque --</option>';
            selModele.innerHTML = '<option value="">-- Choisir le modèle --</option>';
            selModele.disabled  = true;

            chargerMarques(typeSelectionne);

            /* Scroll vers le stepper */
            document.getElementById('instant-quote')
                    .scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    /* ── Select Marque → charger les modèles ─────────────────── */
    if (selMarque) {
        selMarque.addEventListener('change', () => {
            const marque = selMarque.value;

            selModele.innerHTML = '<option value="">-- Choisir le modèle --</option>';
            selModele.disabled  = true;
            if (!marque) return;

            chargerModeles(typeSelectionne, marque);
        });
    }

    /* ── Select Modèle → passer automatiquement au step Réparations ── */
    if (selModele) {
        selModele.addEventListener('change', () => {
            const marque = selMarque.value;
            const modele = selModele.value;

            if (!typeSelectionne || !marque || !modele) return;

            /* Mémoriser pour l'étape produits */
            marqueSelectionnee = marque;
            modeleSelectionne  = modele;

            /* Activer le step 3 dans le stepper */
            activerStep(3);

            /* Charger le catalogue de réparations */
            if (typeof chargerCatalogue === 'function') {
                chargerCatalogue(typeSelectionne, marque, modele);
            }

            /* Afficher la section catalogue et masquer le panel modèle */
            panelModele.classList.add('d-none');
            const catalogueSection = document.getElementById('catalogue-section');
            if (catalogueSection) {
                catalogueSection.classList.remove('d-none');
                catalogueSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    /* ── Fonctions de chargement (à adapter à votre API) ─────── */

    /**
     * Charge les marques pour un type d'appareil donné.
     * Remplacez l'URL par votre endpoint réel.
     */

    function chargerMarques(type) {
        if (modeleLoader) modeleLoader.classList.remove('d-none');
        selMarque.disabled = true;

        const marquesAutorisees = typeMarqueMap[type] || [];

        const typeDB = typeAppareilMap[type] || type;
        const params = new URLSearchParams({ type: typeDB });

        fetch('/api/marques?' + params.toString(), { credentials: 'same-origin' })
            .then(r => r.ok ? r.json() : Promise.reject(r))
            .then(data => {
                selMarque.innerHTML = '<option value="">-- Choisir la marque --</option>';
                (data || [])
                    .filter(m => marquesAutorisees.length === 0 || marquesAutorisees.includes(m))
                    .forEach(m => {
                        const opt = document.createElement('option');
                        opt.value = m; opt.textContent = m;
                        selMarque.appendChild(opt);
                    });
            })
            .catch(err => {
                console.error('Erreur chargement marques', err);
                selMarque.innerHTML = '<option value="">-- Erreur de chargement --</option>';
            })
            .finally(() => {
                selMarque.disabled = false;
                if (modeleLoader) modeleLoader.classList.add('d-none');
            });
    }

    function chargerModeles(type, marque) {
        if (modeleLoader) modeleLoader.classList.remove('d-none');

        const typeDB = typeAppareilMap[type] || '';
        const params = new URLSearchParams();
        if (typeDB) params.set('type', typeDB);
        if (marque) params.set('marque', marque);
        const url = '/api/modeles?' + params.toString();

        fetch(url, { credentials: 'same-origin' })
            .then(r => r.ok ? r.json() : Promise.reject(r))
            .then(data => {
                selModele.innerHTML = '<option value="">-- Choisir le modèle --</option>';
                (data || []).forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m; opt.textContent = m;
                    selModele.appendChild(opt);
                });
                selModele.disabled = false;
            })
            .catch(err => {
                console.error('Erreur chargement modèles', err);
                selModele.innerHTML = '<option value="">-- Erreur de chargement --</option>';
            })
            .finally(() => {
                if (modeleLoader) modeleLoader.classList.add('d-none');
            });
    }

    /* ── Cards Réparation → multi-sélection + bouton Valider ─── */
    (function () {
        const repairGrid            = document.getElementById('repair-cards-grid');
        const validerWrap           = document.getElementById('repair-valider-wrap');
        const repairProductExamples = document.getElementById('repair-product-examples');
        const btnValider            = document.getElementById('btn-valider-reparations');

        /* Limite maximale de réparations sélectionnables */
        const MAX_SELECTIONS = 5;

        /* ── Mise à jour du bouton Valider ── */
        function updateValider() {
            const nb = document.querySelectorAll('#repair-cards-grid .repair-card.device-card--selected').length;
            if (validerWrap) {
                validerWrap.style.display = nb > 0 ? 'block' : 'none';
            }
            const btn = document.getElementById('btn-valider-reparations');
            if (btn) {
                btn.textContent = nb > 1
                    ? `Valider mes ${nb} réparations →`
                    : 'Valider ma réparation →';
            }
        }

        /* ── Délégation d'événements sur document ──
           Attaché sur document pour rester valide même si chargerCatalogue
           recrée ou remplace le contenu de #repair-cards-grid. */
        document.addEventListener('click', (e) => {
            /* On vérifie que le clic est bien à l'intérieur de #repair-cards-grid */
            const grid = document.getElementById('repair-cards-grid');
            if (!grid) return;
            const card = e.target.closest('.repair-card');
            if (!card || !grid.contains(card)) return;

            const isSelected = card.classList.contains('device-card--selected');

            if (isSelected) {
                /* Désélectionner */
                card.classList.remove('device-card--selected');
                card.setAttribute('aria-pressed', 'false');
            } else {
                /* Vérifier la limite avant de sélectionner */
                const nbSelected = document.querySelectorAll('#repair-cards-grid .repair-card.device-card--selected').length;
                if (nbSelected >= MAX_SELECTIONS) {
                    /* Feedback visuel discret : secouer la grille */
                    grid.classList.add('shake');
                    setTimeout(() => grid.classList.remove('shake'), 400);
                    return;
                }
                card.classList.add('device-card--selected');
                card.setAttribute('aria-pressed', 'true');
            }

            updateValider();
        });

        /* Accessibilité clavier (Entrée / Espace) */
        document.addEventListener('keydown', (e) => {
            if (e.key !== 'Enter' && e.key !== ' ') return;
            const grid = document.getElementById('repair-cards-grid');
            if (!grid) return;
            const card = e.target.closest('.repair-card');
            if (!card || !grid.contains(card)) return;
            e.preventDefault();
            card.click();
        });

        /* ── Initialisation : aria-pressed sur les cards statiques ── */
        document.querySelectorAll('#repair-cards-grid .repair-card').forEach(card => {
            if (!card.hasAttribute('aria-pressed')) {
                card.setAttribute('aria-pressed', 'false');
            }
        });

        /* ── Bouton Valider (Problèmes → Réparations) ── */
        if (btnValider) {
            btnValider.addEventListener('click', () => {
                const selected = document.querySelectorAll('#repair-cards-grid .repair-card.device-card--selected');
                if (selected.length < 1) {
                    alert('Veuillez sélectionner au moins une réparation pour valider.');
                    return;
                }

                if (typeof activerStep === 'function') {
                    activerStep(4);
                }

                const reparationsSelectionnees = Array.from(selected).map(c => c.dataset.reparation);
                console.log('Problèmes validés :', reparationsSelectionnees);

                /* Charger les produits filtrés par modèle + réparations sélectionnées */
                if (typeof window.chargerProduits === 'function') {
                    window.chargerProduits(
                        typeSelectionne,
                        marqueSelectionnee,
                        modeleSelectionne,
                        reparationsSelectionnees
                    );
                }
            });

            btnValider.addEventListener('mouseenter', () => btnValider.style.opacity = '0.88');
            btnValider.addEventListener('mouseleave', () => btnValider.style.opacity = '1');
        }
    })();

    /* ── Chargement dynamique des produits (étape Réparations) ── */
    window.chargerProduits = function (type, marque, modele, reparations) {
        const productGrid   = document.getElementById('product-cards-grid');
        const loader        = document.getElementById('product-loader');
        const emptyMsg      = document.getElementById('product-empty');
        const validerWrap   = document.getElementById('product-valider-wrap');

        if (!productGrid) return;

        /* Réinitialiser */
        productGrid.innerHTML = '';
        if (loader)     loader.classList.remove('d-none');
        if (emptyMsg)   emptyMsg.classList.add('d-none');
        if (validerWrap) validerWrap.style.display = 'none';

        const typeDB = typeAppareilMap[type] || type;
        const params = new URLSearchParams({
            type    : typeDB,
            marque  : marque,
            modele  : modele,
        });
        /* Envoyer chaque réparation comme param répété : reparations[]=... */
        (reparations || []).forEach(r => params.append('reparations[]', r));

        fetch('/api/produits?' + params.toString(), { credentials: 'same-origin' })
            .then(r => r.ok ? r.json() : Promise.reject(r))
            .then(data => {
                if (loader) loader.classList.add('d-none');

                if (!data || data.length === 0) {
                    if (emptyMsg) emptyMsg.classList.remove('d-none');
                    return;
                }

                data.forEach(produit => {
                    const imgSrc = produit.imageurl || '/assets/img/default-product.jpg';
                    const btn = document.createElement('button');
                    btn.className   = 'device-card product-example-card';
                    btn.type        = 'button';
                    btn.role        = 'listitem';
                    btn.setAttribute('aria-pressed', 'false');
                    btn.setAttribute('aria-label', produit.nom);
                    btn.dataset.prix    = produit.prixclientttc;
                    btn.dataset.produitId = produit.id;
                    btn.innerHTML = `
                        <img class="product-example-card__image" src="${imgSrc}" alt="${produit.nom}" />
                        <span class="device-card__label">${produit.nom}</span>
                        <small class="text-muted d-block mt-2">${marque} • ${modele}</small>
                        <strong class="product-prix d-block mt-2">${produit.prixclientttc} €</strong>
                    `;
                    productGrid.appendChild(btn);
                });
            })
            .catch(err => {
                console.error('Erreur chargement produits', err);
                if (loader)   loader.classList.add('d-none');
                if (emptyMsg) {
                    emptyMsg.textContent = 'Erreur lors du chargement des produits.';
                    emptyMsg.classList.remove('d-none');
                }
            });
    };

    /* ── Sélection des produits (étape Réparations) ─────────── */
    (function () {
        const validerWrap    = document.getElementById('product-valider-wrap');
        const totalEl        = document.getElementById('product-total');
        const btnValider     = document.getElementById('btn-valider-produits');

        function updateTotal() {
            const productGrid = document.getElementById('product-cards-grid');
            const selected = productGrid
                ? productGrid.querySelectorAll('.product-example-card.device-card--selected')
                : [];
            const total = Array.from(selected).reduce((sum, c) => sum + Number(c.dataset.prix || 0), 0);
            const nb    = selected.length;

            if (validerWrap) validerWrap.style.display = nb > 0 ? 'block' : 'none';
            if (totalEl)     totalEl.textContent = total + ' €';
            if (btnValider)  btnValider.textContent = nb > 1
                ? `Valider mes ${nb} produits →`
                : 'Valider mon produit →';
        }

        /* Délégation sur document pour fonctionner avec le grid rechargé dynamiquement */
        document.addEventListener('click', (e) => {
            const grid = document.getElementById('product-cards-grid');
            if (!grid) return;
            const card = e.target.closest('.product-example-card');
            if (!card || !grid.contains(card)) return;
            const isSelected = card.classList.contains('device-card--selected');
            card.classList.toggle('device-card--selected', !isSelected);
            card.setAttribute('aria-pressed', String(!isSelected));
            updateTotal();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key !== 'Enter' && e.key !== ' ') return;
            const grid = document.getElementById('product-cards-grid');
            if (!grid) return;
            const card = e.target.closest('.product-example-card');
            if (!card || !grid.contains(card)) return;
            e.preventDefault();
            card.click();
        });

        if (btnValider) {
            btnValider.addEventListener('click', () => {
                const productGrid = document.getElementById('product-cards-grid');
                const selected = productGrid
                    ? productGrid.querySelectorAll('.product-example-card.device-card--selected')
                    : [];
                if (selected.length < 1) {
                    alert('Veuillez sélectionner au moins un produit.');
                    return;
                }
                if (typeof window.remplirPanier === 'function') window.remplirPanier();
                if (typeof activerStep === 'function') activerStep(5);
            });
        }
    })();

    /* ── Panier (step 5) ─────────────────────────────────────── */
    (function () {
        const MAX_QTY = 5;
        let panier = [];

        window.remplirPanier = function () {
            const grid = document.getElementById('product-cards-grid');
            if (!grid) return;
            const selected = grid.querySelectorAll('.product-example-card.device-card--selected');

            /* Conserver les quantités déjà saisies */
            const qtesExistantes = {};
            panier.forEach(p => { qtesExistantes[p.id] = p.qty; });

            panier = Array.from(selected).map(card => {
                const id = card.dataset.produitId || card.dataset.prix;
                return {
                    id,
                    nom  : card.querySelector('.device-card__label')?.textContent.trim() || 'Produit',
                    sous : card.querySelector('small')?.textContent.trim() || '',
                    img  : card.querySelector('img')?.src || '',
                    prix : Number(card.dataset.prix || 0),
                    qty  : qtesExistantes[id] || 1,
                };
            });
            renderPanier();
        };

        function renderPanier() {
            const liste      = document.getElementById('panier-liste');
            const vide       = document.getElementById('panier-vide');
            const totalEl    = document.getElementById('panier-total');
            const btnValider = document.getElementById('btn-valider-panier');
            if (!liste) return;

            liste.innerHTML = '';

            if (panier.length === 0) {
                liste.style.display = 'none';
                if (vide)       vide.classList.remove('d-none');
                if (totalEl)    totalEl.textContent = '0 €';
                if (btnValider) { btnValider.disabled = true; btnValider.style.opacity = '0.4'; }
                return;
            }

            liste.style.display = 'flex';
            if (vide)       vide.classList.add('d-none');
            if (btnValider) { btnValider.disabled = false; btnValider.style.opacity = '1'; }

            let total = 0;
            panier.forEach(item => {
                total += item.prix * item.qty;
                const row = document.createElement('div');
                row.className = 'panier-item';
                row.dataset.id = item.id;
                row.innerHTML = `
                    <img class="panier-item__img" src="${item.img}" alt="${item.nom}">
                    <div class="panier-item__info">
                        <div class="panier-item__name">${item.nom}</div>
                        <div class="panier-item__sub">${item.sous}</div>
                        <div class="panier-item__prix">${(item.prix * item.qty).toFixed(2)} €</div>
                    </div>
                    <div class="panier-item__qty">
                        <button class="panier-qty-btn" data-action="moins" aria-label="Diminuer">−</button>
                        <span class="panier-qty-val">${item.qty}</span>
                        <button class="panier-qty-btn" data-action="plus" aria-label="Augmenter">+</button>
                    </div>
                    <button class="panier-item__delete" aria-label="Supprimer ${item.nom}">🗑</button>
                `;
                liste.appendChild(row);
            });

            if (totalEl) totalEl.textContent = total.toFixed(2) + ' €';
        }

        /* Délégation sur la liste */
        document.addEventListener('click', (e) => {
            const row = e.target.closest('.panier-item');
            if (!row) return;
            const idx = panier.findIndex(p => p.id === row.dataset.id);
            if (idx === -1) return;

            if (e.target.closest('.panier-item__delete')) {
                panier.splice(idx, 1);
                renderPanier();
                return;
            }
            const action = e.target.closest('.panier-qty-btn')?.dataset.action;
            if (action === 'plus'  && panier[idx].qty < MAX_QTY) { panier[idx].qty++; renderPanier(); }
            if (action === 'moins' && panier[idx].qty > 1)        { panier[idx].qty--; renderPanier(); }
        });

        const btnValider = document.getElementById('btn-valider-panier');
        if (btnValider) {
            btnValider.addEventListener('click', () => {
                if (typeof activerStep === 'function') activerStep(6);
            });
        }
    })();

    /* ── Coordonnées (step 6) + génération devis ────────────── */
    (function () {

        function validerChamp(input, errEl, testFn, msg) {
            if (!testFn(input.value.trim())) {
                input.classList.add('is-invalid');
                if (errEl) errEl.textContent = msg;
                return false;
            }
            input.classList.remove('is-invalid');
            if (errEl) errEl.textContent = '';
            return true;
        }

        const btn = document.getElementById('btn-valider-coordonnees');
        if (!btn) return;

        btn.addEventListener('click', async () => {
            const nom     = document.getElementById('coord-nom');
            const email   = document.getElementById('coord-email');
            const tel     = document.getElementById('coord-tel');
            const cp      = document.getElementById('coord-cp');
            const adresse = document.getElementById('coord-adresse');

            const ok = [
                validerChamp(nom,     document.getElementById('err-nom'),     v => v.length >= 2,                         'Veuillez saisir votre nom.'),
                validerChamp(email,   document.getElementById('err-email'),   v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v), 'Adresse e-mail invalide.'),
                validerChamp(tel,     document.getElementById('err-tel'),     v => /^[\d\s\+\-\.]{8,}$/.test(v),          'Numéro de téléphone invalide.'),
                validerChamp(cp,      document.getElementById('err-cp'),      v => /^\d{5}$/.test(v),                     'Code postal invalide (5 chiffres).'),
                validerChamp(adresse, document.getElementById('err-adresse'), v => v.length >= 5,                         'Veuillez saisir votre adresse.'),
            ].every(Boolean);

            if (!ok) return;

            /* Collecter les IDs des produits du panier */
            const panierGrid = document.getElementById('product-cards-grid');
            const produitIds = panierGrid
                ? Array.from(panierGrid.querySelectorAll('.product-example-card.device-card--selected'))
                      .map(c => c.dataset.produitId)
                      .filter(Boolean)
                : [];

            if (produitIds.length === 0) {
                alert('Votre panier est vide. Veuillez sélectionner au moins un produit.');
                return;
            }

            /* Désactiver le bouton pendant la requête */
            btn.disabled = true;
            btn.textContent = 'Génération en cours…';

            const body = new FormData();
            body.append('nom',          nom.value.trim());
            body.append('email',        email.value.trim());
            body.append('telephone',    tel.value.trim());
            body.append('adresse_cp',   cp.value.trim());
            body.append('adresse_rue',  adresse.value.trim());
            body.append('adresse_ville', '');
            body.append('panier',       JSON.stringify(produitIds));

            try {
                const response = await fetch('/api/devis', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body,
                });

                if (!response.ok) {
                    const err = await response.json().catch(() => ({}));
                    const msgs = err.errors ? Object.values(err.errors).join('\n') : 'Une erreur est survenue.';
                    alert(msgs);
                    return;
                }

                /* Télécharger le PDF retourné */
                const blob = await response.blob();
                const url  = URL.createObjectURL(blob);
                const a    = document.createElement('a');
                a.href     = url;

                /* Récupérer le nom de fichier depuis Content-Disposition si disponible */
                const cd       = response.headers.get('Content-Disposition') || '';
                const match    = cd.match(/filename="([^"]+)"/);
                a.download     = match ? match[1] : 'devis.pdf';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);

                /* Passer au step confirmation */
                if (typeof activerStep === 'function') activerStep(7);

            } catch (e) {
                console.error('Erreur génération devis', e);
                alert('Une erreur réseau est survenue. Veuillez réessayer.');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Recevoir mon devis →';
            }
        });

        /* Nettoyage erreur au focus */
        ['coord-nom','coord-email','coord-tel','coord-cp','coord-adresse'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('input', () => {
                el.classList.remove('is-invalid');
                const err = document.getElementById('err-' + id.replace('coord-', ''));
                if (err) err.textContent = '';
            });
        });

    })();

})();



/* ----------------------------------------------------------------
   Bouton "Remonter en haut"
   Apparaît après 300 px de défilement.
---------------------------------------------------------------- */
(function () {
    const btnTop = document.getElementById('btn-top');
    if (!btnTop) return;

    window.addEventListener('scroll', () => {
        btnTop.classList.toggle('is-visible', window.scrollY > 300);
    }, { passive: true });

    btnTop.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
})();