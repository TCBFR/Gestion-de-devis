<?php
/**
 * src/Models/ProduitModel.php
 * ----------------------------
 * Toutes les requêtes liées à la table "produit".
 * RÈGLE DE SÉCURITÉ : prix_fourn_ht n'est JAMAIS retourné vers le client.
 */

namespace Biscaphone\Models;

use Biscaphone\Database;
use PDO;

class ProduitModel
{
    private const NOM_MAX_LENGTH = 200;
    private const IMAGE_URL_MAX_LENGTH = 500;

    private PDO $db;

    public function __construct()
    {
        $this->db = Database::get();
    }

    public function getByModele(string $type, string $marque, string $modele): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.id, p.nom, p.prix_client_ttc, p.image_url
             FROM produit p
             JOIN modele m ON m.id = p.modele_id
             WHERE m.type_appareil = :type
               AND m.marque        = :marque
               AND m.modele        = :modele
             ORDER BY p.nom'
        );
        $stmt->execute([':type' => $type, ':marque' => $marque, ':modele' => $modele]);
        return $stmt->fetchAll();
    }

    /**
     * Table de correspondance entre la clé de réparation (data-reparation dans le HTML)
     * et les préfixes exacts des noms de produits en base.
     * Basée sur l'export catalogue réel du fournisseur.
     */
    private const REPARATION_PREFIXES = [
        'ecran' => [
            'Ecran Tactile',
            'Ecran LCD',
            'Vitre Tactile',
        ],
        'vitre-arriere' => [
            'Adhésif Vitre Arrière',
            'Cache Arrière',
            'Vitre Arrière',
            'Coque de Réparation',
            'Châssis Intermédiaire',
        ],
        'batterie' => [
            'Batterie',
        ],
        'connecteur' => [
            'Connecteur de Charge',
        ],
        'camera' => [
            'Appareil Photo',
            'Caméra Visio',
        ],
        'haut-parleur' => [
            'Haut-Parleur',
            'Ecouteur',
            'Nappe Ecouteur',
        ],
        'bouton' => [
            'Bouton On/Off',
            'Bouton Volume',
            'Bouton Home',
            'Nappe Bouton',
        ],
        'degat-eau' => [
            // Dégât des eaux : on affiche tout le catalogue du modèle
            // (le filtre sera ignoré → fallback getByModele)
        ],
    ];

    /**
     * Retourne les produits correspondant au modèle ET pertinents
     * par rapport aux réparations sélectionnées.
     *
     * Le filtrage s'appuie sur une whitelist de préfixes de noms de produits
     * issue du catalogue fournisseur réel — pas d'heuristique sur les mots.
     *
     * @param string   $type        Type d'appareil (ex: "Téléphonie")
     * @param string   $marque      Marque (ex: "Apple")
     * @param string   $modele      Modèle (ex: "iPhone 16")
     * @param string[] $reparations Clés de réparation sélectionnées (ex: ["ecran", "batterie"])
     */
    public function getByModeleEtReparations(string $type, string $marque, string $modele, array $reparations): array
    {
        // Collecter tous les préfixes des réparations sélectionnées
        $prefixes = [];
        $fallbackAll = false;

        foreach ($reparations as $key) {
            $key = trim($key);
            if (!array_key_exists($key, self::REPARATION_PREFIXES)) {
                continue;
            }
            $mapped = self::REPARATION_PREFIXES[$key];
            if (empty($mapped)) {
                // Clé mappée sur tableau vide = fallback tout catalogue (ex: degat-eau)
                $fallbackAll = true;
                break;
            }
            foreach ($mapped as $prefix) {
                $prefixes[] = $prefix;
            }
        }

        // Aucune réparation reconnue ou dégât des eaux → tout le catalogue du modèle
        if ($fallbackAll || empty($prefixes)) {
            return $this->getByModele($type, $marque, $modele);
        }

        // Construire les conditions LIKE sur les préfixes
        $conditions = [];
        $bindings   = [':type' => $type, ':marque' => $marque, ':modele' => $modele];

        foreach (array_unique($prefixes) as $i => $prefix) {
            $param              = ':pfx' . $i;
            $conditions[]       = 'p.nom LIKE ' . $param;
            $bindings[$param]   = $prefix . '%';
        }

        $whereClause = implode(' OR ', $conditions);

        $stmt = $this->db->prepare(
            'SELECT p.id, p.nom, p.prix_client_ttc, p.image_url
             FROM produit p
             JOIN modele m ON m.id = p.modele_id
             WHERE m.type_appareil = :type
               AND m.marque        = :marque
               AND m.modele        = :modele
               AND (' . $whereClause . ')
             ORDER BY p.nom'
        );
        $stmt->execute($bindings);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, nom, prix_client_ttc FROM produit WHERE id = :id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function upsert(array $data): void
    {
        $modeleModel = new ModeleModel();
        $modeleId = $modeleModel->findOrCreate(
            $data['type_appareil'],
            $data['marque'],
            $data['modele']
        );

        $nom = mb_substr(trim($data['nom']), 0, self::NOM_MAX_LENGTH);
        $imageUrl = isset($data['image_url']) ? trim($data['image_url']) : null;
        if ($imageUrl !== null && mb_strlen($imageUrl) > self::IMAGE_URL_MAX_LENGTH) {
            $imageUrl = mb_substr($imageUrl, 0, self::IMAGE_URL_MAX_LENGTH);
        }

        $stmt = $this->db->prepare(
            'INSERT INTO produit (modele_id, nom, prix_fourn_ht, image_url)
             VALUES (:modele_id, :nom, :ht, :img)
             ON DUPLICATE KEY UPDATE
                prix_fourn_ht = VALUES(prix_fourn_ht),
                image_url     = VALUES(image_url),
                updated_at    = CURRENT_TIMESTAMP'
        );
        $stmt->execute([
            ':modele_id' => $modeleId,
            ':nom'       => $nom,
            ':ht'        => $data['prix_fourn_ht'],
            ':img'       => $imageUrl,
        ]);
    }

    public function getAllForAdmin(): array
    {
        return $this->db->query(
            'SELECT m.type_appareil, m.marque, m.modele,
                    p.nom, p.prix_fourn_ht, p.prix_client_ttc, p.image_url
             FROM produit p
             JOIN modele m ON m.id = p.modele_id
             ORDER BY m.type_appareil, m.marque, m.modele, p.nom'
        )->fetchAll();
    }

    public function calculerPrixTtc(float $prixHt): float
    {
        return ceil($prixHt * 1.20 * 1.10);
    }
}