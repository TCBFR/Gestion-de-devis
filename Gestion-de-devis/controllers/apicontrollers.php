<?php
/**
 * src/Controllers/ApiController.php
 * -----------------------------------
 * Endpoints JSON consommés par le JavaScript côté client.
 * Alimente les 3 dropdowns de sélection et le catalogue de produits.
 * RÈGLE : prix_fourn_ht n'est JAMAIS présent dans les réponses.
 */

namespace Biscaphone\Controllers;

use Biscaphone\Models\ModeleModel;
use Biscaphone\Models\ProduitModel;

class ApiController
{
    private ?ModeleModel $modeleModel = null;
    private ?ProduitModel $produitModel = null;

    // GET /api/types → ["Smartphone", "Tablette", ...]
    public function types(): void
    {
        try {
            $types = $this->modeleModel()->getTypes();
        } catch (\Throwable) {
            $types = [];
        }

        $this->json($types ?: ['Téléphonie', 'Tablette', 'Ordinateur']);
    }

    // GET /api/marques?type=Smartphone → ["Apple", "Samsung", ...]
    public function marques(): void
    {
        $type = trim($_GET['type'] ?? '');

        try {
            $marques = $this->modeleModel()->getMarques($type);
        } catch (\Throwable) {
            $marques = [];
        }

        // Si la base est indisponible ou vide, proposer des exemples sélectionnables.
        $this->json($marques ?: $this->marquesSimulees($type));
    }

    // GET /api/modeles?type=Smartphone&marque=Apple → ["iPhone 13", ...]
    public function modeles(): void
    {
        $type   = trim($_GET['type']   ?? '');
        $marque = trim($_GET['marque'] ?? '');

        try {
            $modeles = $this->modeleModel()->getModeles($type, $marque);
        } catch (\Throwable) {
            $modeles = [];
        }

        $this->json($modeles ?: $this->modelesSimules($type, $marque));
    }

    // GET /api/produits?type=Smartphone&marque=Apple&modele=iPhone%2014
    // Retourne les pièces disponibles pour ce modèle (prix TTC uniquement)
    public function produits(): void
    {
        $type   = trim($_GET['type']   ?? '');
        $marque = trim($_GET['marque'] ?? '');
        $modele = trim($_GET['modele'] ?? '');

        if ($type === '' || $marque === '' || $modele === '') {
            $this->json(['error' => 'Paramètres type, marque et modele requis'], 400);
            return;
        }

        if (($_GET['simulation'] ?? '') === '1') {
            $this->json($this->produitsSimules($marque, $modele));
            return;
        }

        // Récupérer les réparations sélectionnées (tableau envoyé par JS : reparations[]=ecran&reparations[]=batterie)
        $reparations = $_GET['reparations'] ?? [];
        if (!is_array($reparations)) {
            $reparations = [$reparations];
        }
        $reparations = array_map('trim', array_filter($reparations));

        try {
            if (empty($reparations)) {
                // Aucune réparation précisée → catalogue complet du modèle
                $produits = $this->produitModel()->getByModele($type, $marque, $modele);
            } else {
                $produits = $this->produitModel()->getByModeleEtReparations($type, $marque, $modele, $reparations);
            }
        } catch (\Throwable) {
            $produits = [];
        }

        $this->json($produits ? $this->produitsFlatcase($produits) : $this->produitsSimules($marque, $modele));
    }

    private function marquesSimulees(string $type): array
    {
        return match ($type) {
            'Téléphonie' => ['Samsung', 'Apple', 'Xiaomi', 'Google'],
            'Tablette' => ['Apple', 'Samsung', 'Lenovo'],
            'Ordinateur' => ['Apple', 'Dell', 'Lenovo', 'Asus'],
            default => ['Apple', 'Samsung', 'Lenovo'],
        };
    }

    private function modelesSimules(string $type, string $marque): array
    {
        return match (true) {
            $type === 'Téléphonie' && $marque === 'Apple' => ['iPhone 13', 'iPhone 14', 'iPhone 15'],
            $type === 'Téléphonie' && $marque === 'Samsung' => ['Galaxy S22', 'Galaxy S23', 'Galaxy S24'],
            $type === 'Téléphonie' => ['Redmi Note 12', 'Pixel 8', 'Galaxy A54'],
            $type === 'Tablette' && $marque === 'Apple' => ['iPad 10', 'iPad Air', 'iPad Pro'],
            $type === 'Tablette' => ['Galaxy Tab S9', 'Tab M10', 'MatePad 11'],
            $type === 'Ordinateur' && $marque === 'Apple' => ['MacBook Air M2', 'MacBook Air M3', 'MacBook Pro 14'],
            default => ['Inspiron 15', 'IdeaPad 5', 'VivoBook 15'],
        };
    }

    private function produitsFlatcase(array $produits): array
    {
        return array_map(static function (array $produit): array {
            return [
                'id' => $produit['id'],
                'nom' => $produit['nom'],
                'prixclientttc' => $produit['prix_client_ttc'],
                'imageurl' => $produit['image_url'] ?? null,
            ];
        }, $produits);
    }

    private function produitsSimules(string $marque, string $modele): array
    {
        return [
            [
                'id' => 9001,
                'nom' => 'Ecran LCD ' . $marque . ' ' . $modele,
                'prixclientttc' => 129.90,
                'imageurl' => null,
            ],
            [
                'id' => 9002,
                'nom' => 'Batterie compatible ' . $marque . ' ' . $modele,
                'prixclientttc' => 59.90,
                'imageurl' => null,
            ],
            [
                'id' => 9003,
                'nom' => 'Connecteur de charge ' . $marque . ' ' . $modele,
                'prixclientttc' => 79.90,
                'imageurl' => null,
            ],
        ];
    }

    private function modeleModel(): ModeleModel
    {
        return $this->modeleModel ??= new ModeleModel();
    }

    private function produitModel(): ProduitModel
    {
        return $this->produitModel ??= new ProduitModel();
    }

    // ── Envoie une réponse JSON avec le bon Content-Type ────────
    private function json(mixed $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}