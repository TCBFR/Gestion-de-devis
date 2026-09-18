<?php
/**
 * src/helpers/csvparser.php
 * -------------------------
 * Lecture et traitement d'un fichier CSV fournisseur.
 */

namespace Biscaphone\Helpers;

use Biscaphone\Models\ProduitModel;

class CsvParser
{
    public static function import(string $filePath, ProduitModel $model): array
    {
        $rapport = ['imported' => 0, 'warnings' => [], 'errors' => []];
        $handle = @fopen($filePath, 'r');
        if ($handle === false) {
            $rapport['errors'][] = 'Impossible d\'ouvrir le fichier CSV.';
            return $rapport;
        }

        try {
            $separator = self::detectSeparator($handle);
            $columnMap = self::parseHeaders($handle, $separator, $rapport);
            if (!empty($rapport['errors'])) {
                return $rapport;
            }
            self::processRows($handle, $separator, $columnMap, $model, $rapport);
        } finally {
            fclose($handle);
        }

        return $rapport;
    }

    private static function detectSeparator($handle): string
    {
        $firstLine = fgets($handle);
        rewind($handle);
        if ($firstLine === false) {
            return ';';
        }

        return substr_count($firstLine, ';') >= substr_count($firstLine, ',') ? ';' : ',';
    }

    private static function parseHeaders($handle, string $separator, array &$rapport): array
    {
        $rawHeaders = fgetcsv($handle, 0, $separator, '"', '\\');
        if (empty($rawHeaders)) {
            $rapport['errors'][] = 'Le fichier CSV est vide ou mal formé.';
            return [];
        }

        $columnMap = CsvRowMapper::buildColumnMap($rawHeaders);
        foreach (CsvRowMapper::REQUIRED_COLUMNS as $col) {
            if (!isset($columnMap[$col])) {
                $rapport['errors'][] = "Colonne requise manquante : « $col »";
            }
        }

        return $columnMap;
    }

    private static function processRows($handle, string $separator, array $columnMap, ProduitModel $model, array &$rapport): void
    {
        $lineNumber = 1;
        $previous = ['type_appareil' => '', 'marque' => '', 'modele' => ''];

        while (($row = fgetcsv($handle, 0, $separator, '"', '\\')) !== false) {
            $lineNumber++;
            if (empty(array_filter($row))) {
                continue;
            }

            $row = self::inheritMissingFields($row, $columnMap, $previous);

            try {
                $model->upsert(CsvRowMapper::extractRow($row, $columnMap));
                $rapport['imported']++;
            } catch (\InvalidArgumentException $e) {
                $rapport['warnings'][] = "Ligne $lineNumber ignorée : " . $e->getMessage();
            } catch (\Exception $e) {
                $rapport['errors'][] = "Ligne $lineNumber — erreur : " . $e->getMessage();
            }
        }
    }

    private static function inheritMissingFields(array $row, array $columnMap, array &$previous): array
    {
        foreach (['type_appareil', 'marque', 'modele'] as $field) {
            if (!isset($columnMap[$field])) {
                continue;
            }

            $index = $columnMap[$field];
            $value = trim($row[$index] ?? '');

            if ($value !== '') {
                $previous[$field] = $value;
                continue;
            }

            if ($previous[$field] !== '') {
                $row[$index] = $previous[$field];
            }
        }

        return $row;
    }
}
