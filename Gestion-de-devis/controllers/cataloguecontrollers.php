<?php
/**
 * src/Controllers/CatalogueController.php
 * -----------------------------------------
 * Affiche la page publique du catalogue (interface de sélection).
 * La page est statique au chargement : les dropdowns et les produits
 * sont ensuite peuplés dynamiquement via les appels à l'ApiController.
 */

namespace Biscaphone\Controllers;

class CatalogueController
{
    // GET / → page d'accueil publique avec les 3 dropdowns de sélection
    public function index(): void
    {
        // On passe le nom de la société pour le titre de la page
        $vendeur = config('vendeur');

        $this->render('catalogue/index', compact('vendeur'));
    }

    // GET /instant-quote or /quote — version compacte style "Instant Quote"
    public function instantQuote(): void
    {    
        $vendeur = config('vendeur');
        $this->render('catalogue/profiletype', compact('vendeur'));
    }

    // ── Charge un template de vue dans le layout public ─────────
    private function render(string $view, array $data = []): void
    {
        extract($data);

        $viewFile = VIEWS_DIR . '/' . $view . '.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("Vue introuvable : $viewFile");
        }

        require VIEWS_DIR . '/layouts/header.php';
        require VIEWS_DIR . '/layouts/foot.php';
    }
}