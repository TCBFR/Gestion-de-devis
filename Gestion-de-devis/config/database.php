<?php
/**
 * src/database.php
 * -----------------
 * Connexion MySQL via PDO.
 * Utilise le pattern Singleton pour ne créer qu'une seule connexion
 * par requête HTTP, quelque soit le nombre de modèles qui l'utilisent.
 */

namespace Biscaphone;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}

    // ── Point d'accès unique à la connexion ─────────────────────
    public static function get(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $host     = config('database.host',     '127.0.0.1');
        $port     = config('database.port',     8889);
        $name     = config('database.name',     'biscaphone');
        $user     = config('database.user',     'root');
        $password = config('database.password', 'root');
        $charset  = config('database.charset',  'utf8mb4');
        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";

        try {
            $pdo = new PDO($dsn, $user, $password);

            // Lance une exception PHP en cas d'erreur SQL (évite les échecs silencieux)
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Retourne les résultats sous forme de tableaux associatifs par défaut
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            self::$instance = $pdo;
        } catch (PDOException $e) {
            throw new \RuntimeException('Erreur de connexion à la base de données : ' . $e->getMessage());
        }

        return self::$instance;
    }
}