<?php
/**
 * src/helpers/admincsvexporthelper.php
 * ------------------------------------
 * Export CSV des données administratives.
 */

namespace Biscaphone\Helpers;

class AdminCsvExportHelper
{
    public static function export(array $produits): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="export-bd-' . date('Y-m-d') . '.csv"');

        echo "\xEF\xBB\xBF";
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Type', 'Marque', 'Modèle', 'Nom', 'Prix HT', 'Prix TTC', 'Image'], ';', '"', '\\');

        foreach ($produits as $produit) {
            fputcsv($out, [
                self::sanitize($produit['type_appareil'] ?? ''),
                self::sanitize($produit['marque'] ?? ''),
                self::sanitize($produit['modele'] ?? ''),
                self::sanitize($produit['nom'] ?? ''),
                number_format((float) ($produit['prix_fourn_ht'] ?? 0), 2, ',', ''),
                number_format((float) ($produit['prix_client_ttc'] ?? 0), 2, ',', ''),
                self::sanitize($produit['image_url'] ?? ''),
            ], ';', '"', '\\');
        }

        fclose($out);
    }

    private static function sanitize(string $value): string
    {
        return preg_replace('/[\r\n]+/', ' ', trim($value));
    }
}
