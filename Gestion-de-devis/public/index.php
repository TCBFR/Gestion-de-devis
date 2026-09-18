<?php
/**
 * public/index.php
 * -----------------
 * Point d'entrée unique de l'application (pattern Front Controller).
 * Toutes les requêtes HTTP passent par ici grâce au .htaccess.
 * Ordre : chargement .env → autoloader → configuration → routage.
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);
define('ROOT_DIR', dirname(__DIR__));
define('VIEWS_DIR', ROOT_DIR . '/vues');

// 1. Chargement des variables d'environnement depuis le fichier .env
(function () {
    $envFile = ROOT_DIR . '/.env';
    if (!file_exists($envFile)) {
        return;
    }
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        // Ignorer les commentaires et les lignes vides
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
})();

// 2. Autoloader PSR-4 simple : Biscaphone\Models\Truc → src/Models/Truc.php
// Chargement simple : inclut tous les fichiers PHP des dossiers principaux
foreach (glob(ROOT_DIR . '/controllers/*.php') as $file) {
    require_once $file;
}
foreach (glob(ROOT_DIR . '/helpers/*.php') as $file) {
    require_once $file;
}
foreach (glob(ROOT_DIR . '/model/*.php') as $file) {
    require_once $file;
}
// Charger uniquement les fichiers de configuration nécessaires (évite d'inclure les scripts CLI)
require_once ROOT_DIR . '/config/config.php';
require_once ROOT_DIR . '/config/database.php';
require_once ROOT_DIR . '/config/router.php';

// 3. Fonction helper globale pour lire la config (ex: config('vendeur.nom'))
function config(string $key, mixed $default = null): mixed
{
    static $cfg = null;
    if ($cfg === null) {
        $cfg = require ROOT_DIR . '/config/config.php';
    }
    // Supporte la notation pointée (ex: 'vendeur.email')
    $keys = explode('.', $key);
    $value = $cfg;
    foreach ($keys as $k) {
        if (!is_array($value) || !array_key_exists($k, $value)) {
            return $default;
        }
        $value = $value[$k];
    }
    return $value;
}

// 4. Démarrage du routeur
try {
    $router = new \Biscaphone\Router();
    $router->dispatch();
} catch (\Throwable $e) {
    http_response_code(500);
    echo '<h1>500 — Erreur interne</h1>';
    if (config('app.debug')) {
        echo '<pre>' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</pre>';
    }
}