<?php
/**
 * src/helpers/csvrowmapper.php
 * ----------------------------
 * Cartographie des en-têtes et validation des lignes CSV.
 */

namespace Biscaphone\Helpers;

class CsvRowMapper
{
    public const COLUMN_ALIASES = [
        'type_appareil' => ['type d\'appareil', 'type appareil', 'type', 'type_appareil'],
        'marque'        => ['marque'],
        'modele'        => ['modèle', 'modele', 'model'],
        'nom'           => ['nom', 'name', 'libellé', 'libelle'],
        'prix_fourn_ht' => ['prix-ht', 'prix ht', 'prixht', 'prix_ht', 'prix fourn ht'],
        'image_url'     => ['image', 'image_url', 'img'],
    ];

    public const REQUIRED_COLUMNS = ['type_appareil', 'marque', 'modele', 'nom', 'prix_fourn_ht'];
    public const MAX_NOM_LENGTH = 200;
    public const MAX_IMAGE_URL_LENGTH = 500;

    public static function buildColumnMap(array $rawHeaders): array
    {
        $normalizedHeaders = array_map([self::class, 'normalizeHeader'], $rawHeaders);
        $map = [];

        foreach (self::COLUMN_ALIASES as $canonical => $aliases) {
            foreach ($aliases as $alias) {
                $index = array_search(self::normalizeHeader($alias), $normalizedHeaders, true);
                if ($index !== false) {
                    $map[$canonical] = $index;
                    break;
                }
            }
        }

        return $map;
    }

    public static function extractRow(array $row, array $columnMap): array
    {
        $get = fn(string $col): string => trim($row[$columnMap[$col]] ?? '');

        foreach (['type_appareil', 'marque', 'modele', 'nom'] as $field) {
            if ($get($field) === '') {
                throw new \InvalidArgumentException("Champ obligatoire vide : « $field »");
            }
        }

        $prixRaw = str_replace(',', '.', $get('prix_fourn_ht'));
        if (!is_numeric($prixRaw) || (float) $prixRaw <= 0) {
            throw new \InvalidArgumentException("Prix invalide : « $prixRaw »");
        }

        $nom = $get('nom');
        if (mb_strlen($nom) > self::MAX_NOM_LENGTH) {
            $nom = mb_substr($nom, 0, self::MAX_NOM_LENGTH);
        }

        $imageUrl = $get('image_url');
        if (mb_strlen($imageUrl) > self::MAX_IMAGE_URL_LENGTH) {
            $imageUrl = mb_substr($imageUrl, 0, self::MAX_IMAGE_URL_LENGTH);
        }

        return [
            'type_appareil' => $get('type_appareil'),
            'marque'        => $get('marque'),
            'modele'        => $get('modele'),
            'nom'           => $nom,
            'prix_fourn_ht' => (float) $prixRaw,
            'image_url'     => $imageUrl ?: null,
        ];
    }

    private static function normalizeHeader(string $header): string
    {
        return strtr(mb_strtolower(trim($header)), [
            'é' => 'e', 'è' => 'e', 'ê' => 'e',
            'à' => 'a', 'ô' => 'o', 'î' => 'i', 'ù' => 'u',
        ]);
    }
}
