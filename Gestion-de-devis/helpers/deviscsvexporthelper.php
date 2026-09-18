<?php
/**
 * src/helpers/deviscsvexporthelper.php
 * ------------------------------------
 * Export CSV des devis enregistrés.
 */

namespace Biscaphone\Helpers;

class DevisCsvExportHelper
{
    public static function export(array $devisList): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="devis-export-' . date('Y-m-d') . '.csv"');

        echo "\xEF\xBB\xBF";
        $out = fopen('php://output', 'w');

        foreach ($devisList as $devis) {
            foreach (self::buildRows($devis) as $row) {
                fputcsv($out, $row, ';', '"', '\\');
            }
            fputcsv($out, [], ';', '"', '\\');
        }

        fclose($out);
    }

    public static function exportSingle(array $devis): void
    {
        $filename = 'devis-' . ($devis['id'] ?? 'export') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        echo "\xEF\xBB\xBF";
        $out = fopen('php://output', 'w');

        foreach (self::buildRows($devis) as $row) {
            fputcsv($out, $row, ';', '"', '\\');
        }

        fclose($out);
    }

    private static function buildRows(array $devis): array
    {
        $adresse = trim(implode(' ', array_filter([
            $devis['client']['adresse']['rue'] ?? '',
            $devis['client']['adresse']['cp'] ?? '',
            $devis['client']['adresse']['ville'] ?? '',
        ])));

        $totalHt = 0.0;
        foreach ($devis['produits'] as $produit) {
            $prixTtc = (float) ($produit['prix_client_ttc'] ?? 0);
            $totalHt += $prixTtc / 1.2;
        }
        $totalHt = round($totalHt, 2);
        $totalTva = round(($devis['total_ttc'] ?? 0) - $totalHt, 2);

        $rows = [
            ['DEVIS', $devis['id'] ?? '', '', '', '', 'Date', date('d/m/Y H:i', strtotime($devis['created_at'] ?? ''))],
            ['Client', $devis['client']['nom'] ?? '', '', '', '', 'Téléphone', $devis['client']['telephone'] ?? ''],
            ['Email', $devis['client']['email'] ?? '', '', '', '', 'Adresse', $adresse],
            [],
            ['Description', 'Prix unitaire HT', 'Unité', 'Quantité', 'Montant HT'],
        ];

        foreach ($devis['produits'] as $produit) {
            $prixHt = round(((float) ($produit['prix_client_ttc'] ?? 0)) / 1.2, 2);
            $rows[] = [
                $produit['nom'] ?? '',
                number_format($prixHt, 2, ',', ''),
                'u',
                1,
                number_format($prixHt, 2, ',', ''),
            ];
        }

        $rows[] = [];
        $rows[] = ['', '', '', 'Total HT', number_format($totalHt, 2, ',', '')];
        $rows[] = ['', '', '', 'TVA 20%', number_format($totalTva, 2, ',', '')];
        $rows[] = ['', '', '', 'Total TTC', number_format($devis['total_ttc'] ?? 0, 2, ',', '')];
        $rows[] = [];
        $rows[] = ['Mentions légales :', 'Devis valable 30 jours. Les prix sont indiqués en euros TTC. Contactez-nous pour confirmer votre demande ou obtenir plus d\'informations.'];

        return $rows;
    }
}
