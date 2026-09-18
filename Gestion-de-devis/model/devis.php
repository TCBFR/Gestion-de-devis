<?php
/**
 * src/Models/DevisModel.php
 * --------------------------
 * Gestion du stockage des devis en JSON (un fichier par devis).
 * Chaque devis est identifié par un ID unique basé sur la date + un token aléatoire.
 * Les données sauvegardées respectent le cahier des charges :
 * produits, coordonnées client, date, IP, total TTC.
 */

namespace Biscaphone\Models;

class DevisModel
{
    private string $devisDir;

    public function __construct()
    {
        $this->devisDir = config('storage.devis_dir');

        // Crée le dossier de stockage s'il n'existe pas
        if (!is_dir($this->devisDir)) {
            mkdir($this->devisDir, 0755, true);
        }
    }

    // ── Sauvegarde un devis et retourne son ID ───────────────────
    public function save(array $client, array $produits, float $totalTtc): string
    {
        $id = date('Ymd-His') . '-' . bin2hex(random_bytes(4));

        $devis = [
            'id'         => $id,
            'created_at' => date('Y-m-d H:i:s'),
            'ip'         => $_SERVER['REMOTE_ADDR'] ?? 'inconnue',
            'client'     => [
                // On sauvegarde les coordonnées client (conformément au CdC)
                'nom'       => $client['nom'],
                'telephone' => $client['telephone'],
                'email'     => $client['email'],
                'adresse'   => $client['adresse'] ?? null,
            ],
            'produits'   => $produits,   // [{id, nom, prix_client_ttc}]
            'total_ttc'  => $totalTtc,
        ];

        file_put_contents(
            $this->devisDir . $id . '.json',
            json_encode($devis, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        return $id;
    }

    // ── Charge un devis par son ID ───────────────────────────────
    public function find(string $id): ?array
    {
        // Sécurité : on assainit l'ID pour éviter toute traversée de chemin
        $safeId = preg_replace('/[^a-zA-Z0-9\-]/', '', $id);
        $file   = $this->devisDir . $safeId . '.json';

        if (!file_exists($file)) {
            return null;
        }

        return json_decode(file_get_contents($file), true);
    }

    // ── Liste tous les devis (du plus récent au plus ancien) ─────
    public function all(): array
    {
        $files = glob($this->devisDir . '*.json') ?: [];

        // Trie par date de modification décroissante (plus récent en premier)
        usort($files, fn($a, $b) => filemtime($b) - filemtime($a));

        return array_map(function (string $file) {
            return json_decode(file_get_contents($file), true);
        }, $files);
    }
}