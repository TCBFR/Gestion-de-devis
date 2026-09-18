#!/usr/bin/env php
<?php

define('ROOT_DIR', dirname(__DIR__));
require_once ROOT_DIR . '/config/config.php';
require_once ROOT_DIR . '/config/database.php';
require_once ROOT_DIR . '/model/modele.php';
require_once ROOT_DIR . '/model/produit.php';
require_once ROOT_DIR . '/helpers/csvhelper.php';
require_once ROOT_DIR . '/helpers/csvparser.php';
require_once ROOT_DIR . '/helpers/csvrowmapper.php';

if ($argc !== 2) {
    fwrite(STDERR, "Usage: php importcsv.php /chemin/vers/fichier.csv\n");
    exit(1);
}

$filePath = $argv[1];
if (!is_file($filePath) || !is_readable($filePath)) {
    fwrite(STDERR, "Fichier introuvable ou non lisible : $filePath\n");
    exit(1);
}

$produitModel = new \Biscaphone\Models\ProduitModel();
$rapport = \Biscaphone\Helpers\CsvHelper::import($filePath, $produitModel);

if (!empty($rapport['error'])) {
    fwrite(STDERR, "Erreur : " . $rapport['error'] . "\n");
}

fwrite(STDOUT, "Import terminé. Produits importés : " . ((int) ($rapport['imported'] ?? 0)) . "\n");
if (!empty($rapport['warnings'])) {
    fwrite(STDOUT, "Avertissements :\n");
    foreach ($rapport['warnings'] as $warning) {
        fwrite(STDOUT, " - $warning\n");
    }
}
if (!empty($rapport['errors'])) {
    fwrite(STDOUT, "Erreurs :\n");
    foreach ($rapport['errors'] as $error) {
        fwrite(STDOUT, " - $error\n");
    }
}

exit(!empty($rapport['errors']) ? 1 : 0);
