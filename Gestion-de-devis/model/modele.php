<?php
/**
 * src/Models/ModeleModel.php
 * ---------------------------
 * Toutes les requêtes liées à la table "modele".
 */

namespace Biscaphone\Models;

use Biscaphone\Database;
use PDO;

class ModeleModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::get();
    }

    public function getTypes(): array
    {
        $stmt = $this->db->query('SELECT DISTINCT type_appareil FROM modele ORDER BY type_appareil');
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getMarques(string $type): array
    {
        if ($type === '') {
            $stmt = $this->db->query('SELECT DISTINCT marque FROM modele ORDER BY marque');
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        $stmt = $this->db->prepare(
            'SELECT DISTINCT marque FROM modele WHERE type_appareil = :type ORDER BY marque'
        );
        $stmt->execute([':type' => $type]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getModeles(string $type, string $marque): array
    {
        // Cas : ni type ni marque — renvoyer tous les modèles distincts
        if ($type === '' && $marque === '') {
            $stmt = $this->db->query('SELECT DISTINCT modele FROM modele ORDER BY modele');
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        // Cas : marque fourni mais pas de type — renvoyer modèles pour la marque (toutes catégories)
        if ($type === '' && $marque !== '') {
            $stmt = $this->db->prepare(
                'SELECT DISTINCT modele FROM modele WHERE marque = :marque ORDER BY modele'
            );
            $stmt->execute([':marque' => $marque]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        // Cas standard : filtre par type et marque
        $stmt = $this->db->prepare(
            'SELECT DISTINCT modele FROM modele
             WHERE type_appareil = :type AND marque = :marque
             ORDER BY modele'
        );
        $stmt->execute([':type' => $type, ':marque' => $marque]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function findOrCreate(string $type, string $marque, string $modele): int
    {
        $existing = $this->findId($type, $marque, $modele);
        if ($existing !== null) {
            return $existing;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO modele (type_appareil, marque, modele)
             VALUES (:type, :marque, :modele)'
        );
        $stmt->execute([':type' => $type, ':marque' => $marque, ':modele' => $modele]);

        return (int) $this->db->lastInsertId();
    }

    private function findId(string $type, string $marque, string $modele): ?int
    {
        $stmt = $this->db->prepare(
            'SELECT id FROM modele
             WHERE type_appareil = :type
               AND marque        = :marque
               AND modele        = :modele
             LIMIT 1'
        );
        $stmt->execute([':type' => $type, ':marque' => $marque, ':modele' => $modele]);
        $result = $stmt->fetchColumn();
        return $result !== false ? (int) $result : null;
    }
}
