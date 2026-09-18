<?php
/**
 * src/helpers/importhistoryhelper.php
 * -----------------------------------
 * Historique des imports CSV pour éviter les ré-imports du même fichier.
 */

namespace Biscaphone\Helpers;

class ImportHistoryHelper
{
    public static function isAlreadyImported(string $filePath): bool
    {
        $hash = self::computeHash($filePath);
        $history = self::getHistory();
        return isset($history[$hash]);
    }

    public static function getImportRecord(string $filePath): ?array
    {
        $hash = self::computeHash($filePath);
        $history = self::getHistory();
        return $history[$hash] ?? null;
    }

    public static function markImported(string $filePath, ?string $filename = null): void
    {
        $history = self::getHistory();
        $hash = self::computeHash($filePath);

        if (isset($history[$hash])) {
            return;
        }

        $history[$hash] = [
            'filename'    => $filename ?? basename($filePath),
            'imported_at' => date('c'),
            'hash'        => $hash,
        ];

        self::saveHistory($history);
    }

    private static function getHistory(): array
    {
        $filepath = self::getHistoryFilePath();
        if (!is_file($filepath)) {
            return [];
        }

        $content = file_get_contents($filepath);
        if ($content === false) {
            return [];
        }

        $decoded = json_decode($content, true);
        return is_array($decoded) ? $decoded : [];
    }

    private static function saveHistory(array $history): void
    {
        $filepath = self::getHistoryFilePath();
        $dir = dirname($filepath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($filepath, json_encode($history, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private static function computeHash(string $filePath): string
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new \RuntimeException('Impossible de lire le fichier CSV pour calculer son empreinte.');
        }

        $normalized = str_replace(["\r\n", "\r"], "\n", $content);
        return hash('sha256', $normalized);
    }

    private static function getHistoryFilePath(): string
    {
        return config('storage.import_history_file', ROOT_DIR . '/storage/imports/imported_csv_hashes.json');
    }
}
