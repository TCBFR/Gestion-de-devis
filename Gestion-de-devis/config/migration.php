<?php
/**
 * database/migrate.php
 * ---------------------
 * Script d'installation à exécuter une seule fois en ligne de commande :
 *   php database/migrate.php
 *
 * Actions :
 *   1. Crée la base de données SQLite si elle n'existe pas
 *   2. Exécute le schéma SQL (tables + index)
 *   3. Crée le mot de passe admin par défaut (admin123 — à changer !)
 *   4. Crée les dossiers de stockage nécessaires
 */

define('ROOT_DIR', dirname(__DIR__));

// Charge le .env si présent
(function () {
    $envFile = ROOT_DIR . '/.env';
    if (!file_exists($envFile)) return;
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        putenv("$key=$value");
    }
})();

function config(string $key, mixed $default = null): mixed
{
    static $cfg = null;
    if ($cfg === null) $cfg = require ROOT_DIR . '/config/config.php';
    $keys = explode('.', $key);
    $val = $cfg;
    foreach ($keys as $k) {
        if (!is_array($val) || !array_key_exists($k, $val)) return $default;
        $val = $val[$k];
    }
    return $val;
}

// ── 1. Création des dossiers de stockage ─────────────────────────────
$dossiers = [
    config('storage.devis_dir'),
    config('storage.backup_dir'),
    dirname(config('database.path')),
];

foreach ($dossiers as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "✅ Dossier créé : $dir\n";
    }
}

// ── 2. Création / migration de la base de données ────────────────────
$dbPath = config('database.path');
$pdo    = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$schema = file_get_contents(ROOT_DIR . '/database/schema.sql');
$pdo->exec($schema);
echo "✅ Schéma SQL appliqué : $dbPath\n";

// ── 3. Création du mot de passe admin par défaut ─────────────────────
$passwordFile = config('admin.password_file');

if (!file_exists($passwordFile)) {
    $defaultPassword = 'admin123';
    file_put_contents($passwordFile, password_hash($defaultPassword, PASSWORD_BCRYPT));
    echo "✅ Mot de passe admin créé (défaut : $defaultPassword)\n";
    echo "⚠️  IMPORTANT : changez ce mot de passe depuis l'interface admin !\n";
} else {
    echo "ℹ️  Fichier mot de passe existant — non modifié.\n";
}

echo "\n🎉 Installation terminée. L'application est prête.\n";