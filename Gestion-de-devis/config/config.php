<?php

/**
 * config/config.php
 *
 * Configuration centrale de BiscaPhone.
 * Toutes les valeurs sensibles viennent du .env via env().
 * Accès : config('section.clé')  →  voir helper config() en bas de fichier.
 */

// ── Helper .env ───────────────────────────────────────────────────────────────
if (!function_exists('env')) {
    function env(string $key, mixed $default = ''): mixed
    {
        $value = getenv($key);
        return $value !== false ? $value : $default;
    }
}

// ── Helper config() ───────────────────────────────────────────────────────────
if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        static $cfg = null;
        $cfg ??= require __FILE__;

        $keys  = explode('.', $key);
        $value = $cfg;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }
}

// ── Configuration ─────────────────────────────────────────────────────────────
return [

    // Application
    'app' => [
        'name'   => 'BiscaPhone Tarifs',
        'domain' => env('DOMAIN', 'tarifs.biscaphone.fr'),
        'debug'  => env('APP_DEBUG') === 'true',
    ],

    // Informations vendeur — imprimées sur chaque devis PDF
    'vendeur' => [
        'societe'   => env('VENDEUR_SOCIETE', 'BISCAPHONE'),
        'nom'       => env('VENDEUR_NOM'),
        'adresse'   => env('VENDEUR_ADRESSE'),
        'email'     => env('VENDEUR_EMAIL'),
        'telephone' => env('VENDEUR_TELEPHONE'),
        'siret'     => env('VENDEUR_SIRET'),
        'site_web'  => env('VENDEUR_SITE_WEB'),
        'logo_url'  => env('VENDEUR_IMAGE'),
        'maps_url'  => env('VENDEUR_GPS'),
    ],

    // Lignes fixes ajoutées automatiquement sur chaque devis
    'devis' => [
        'livraison_ttc'   => 10.00,
        'main_oeuvre_ttc' => 50.00,
        'validite_jours'  => 30,
    ],

    // Base de données MySQL
    'database' => [
        'driver'       => 'mysql',
        'host'         => env('DB_HOST', '127.0.0.1'),
        'port'         => (int) env('DB_PORT', 8889),
        'name'         => env('DB_NAME', 'biscaphone'),
        'user'         => env('DB_USER', 'root'),
        'password'     => env('DB_PASSWORD', 'root'),
        'charset'      => 'utf8mb4',
        'mysqldump_bin' => env('MYSQLDUMP_BIN', 'mysqldump'),
    ],

    // Dossiers de stockage
    'storage' => [
        'devis_dir'          => ROOT_DIR . '/storage/devis/',
        'backup_dir'         => ROOT_DIR . '/storage/backups/',
        'import_dir'         => ROOT_DIR . '/storage/imports/',
        'import_history_file' => ROOT_DIR . '/storage/imports/imported_csv_hashes.json',
    ],

    // Génération PDF
    'wkhtmltopdf' => [
        'bin' => env('WKHTMLTOPDF_BIN', '/usr/local/bin/wkhtmltopdf'),
    ],

    // Authentification admin (hash bcrypt stocké sur disque)
    'admin' => [
        'password_file' => ROOT_DIR . '/storage/admin_password.hash',
    ],

];