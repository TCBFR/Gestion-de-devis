<?php
/**
 * src/helpers/csvhelper.php
 * -------------------------
 * Point d'entrée pour l'import CSV fournisseur.
 */

namespace Biscaphone\Helpers;

use Biscaphone\Models\ProduitModel;

class CsvHelper
{
    public static function import(string $filePath, ProduitModel $model): array
    {
        return CsvParser::import($filePath, $model);
    }
}
