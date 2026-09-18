<?php
namespace Biscaphone\Controllers;

use Biscaphone\Helpers\AdminBackupHelper;
use Biscaphone\Helpers\CsvHelper;
use Biscaphone\Helpers\ImportHistoryHelper;

class AdminImportController extends AdminBaseController
{
    public function importForm(): void
    {
        $this->requireAuth();
        $importDir = config('storage.import_dir');
        if (!is_dir($importDir)) {
            mkdir($importDir, 0755, true);
        }

        $importFiles = array_values(array_filter(scandir($importDir), function (string $name) {
            return str_ends_with($name, '.csv');
        }));

        $this->render('admin/importsviews', ['rapport' => null, 'importFiles' => $importFiles]);
    }

    public function import(): void
    {
        $this->requireAuth();
        $importDir = config('storage.import_dir');
        if (!is_dir($importDir)) {
            mkdir($importDir, 0755, true);
        }

        $importFiles = array_values(array_filter(scandir($importDir), function (string $name) {
            return str_ends_with($name, '.csv');
        }));

        $serverFile = trim($_POST['existing_file'] ?? '');
        $file = $_FILES['csv'] ?? null;
        $csvPath = null;

        if ($serverFile !== '') {
            $serverFile = basename($serverFile);
            $candidate = $importDir . $serverFile;
            if (!is_file($candidate) || !is_readable($candidate)) {
                $this->render('admin/importsviews', ['rapport' => ['error' => 'Fichier serveur introuvable ou non lisible.'], 'importFiles' => $importFiles]);
                return;
            }
            $csvPath = $candidate;
        }

        if ($csvPath === null) {
            if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                $this->render('admin/importsviews', ['rapport' => ['error' => 'Aucun fichier reçu.'], 'importFiles' => $importFiles]);
                return;
            }

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $messages = [
                    UPLOAD_ERR_INI_SIZE   => 'Fichier trop volumineux (limite php.ini : ' . ini_get('upload_max_filesize') . ')',
                    UPLOAD_ERR_FORM_SIZE  => 'Fichier trop volumineux (limite formulaire)',
                    UPLOAD_ERR_PARTIAL    => 'Fichier reçu partiellement',
                    UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant',
                    UPLOAD_ERR_CANT_WRITE => 'Impossible d\'écrire le fichier temporaire',
                ];
                $msg = $messages[$file['error']] ?? 'Erreur upload (code ' . $file['error'] . ')';
                $this->render('admin/importsviews', ['rapport' => ['error' => $msg], 'importFiles' => $importFiles]);
                return;
            }

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($ext !== 'csv') {
                $this->render('admin/importsviews', ['rapport' => ['error' => 'Le fichier doit avoir l\'extension .csv'], 'importFiles' => $importFiles]);
                return;
            }

            // Si l'utilisateur souhaite téléverser le fichier dans le dossier serveur
            if (!empty($_POST['save_server'])) {
                $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($file['name']));
                $destName = time() . '-' . $safeName;
                $dest = $importDir . $destName;

                if (!move_uploaded_file($file['tmp_name'], $dest)) {
                    $this->render('admin/importsviews', ['rapport' => ['error' => 'Échec du téléversement sur le serveur.'], 'importFiles' => $importFiles]);
                    return;
                }

                // Mettre à jour la liste des fichiers disponibles
                $importFiles = array_values(array_filter(scandir($importDir), function (string $name) {
                    return str_ends_with($name, '.csv');
                }));

                $this->render('admin/importsviews', ['rapport' => ['imported' => 0, 'warnings' => [], 'errors' => [], 'message' => 'Fichier téléversé avec succès : ' . $destName], 'importFiles' => $importFiles]);
                return;
            }

            $csvPath = $file['tmp_name'];
        }

        if (ImportHistoryHelper::isAlreadyImported($csvPath)) {
            $record = ImportHistoryHelper::getImportRecord($csvPath);
            $message = 'Ce fichier CSV a déjà été importé';
            if ($record !== null && !empty($record['imported_at'])) {
                $message .= ' le ' . $record['imported_at'];
            }
            $this->render('admin/importsviews', [
                'rapport' => ['error' => $message . '.'],
                'importFiles' => $importFiles,
            ]);
            return;
        }

        // Sauvegarde automatique avant l'import, comme demandé dans le cahier des charges.
        $backupWarning = null;
        try {
            AdminBackupHelper::saveBackup();
        } catch (\Throwable $e) {
            $backupWarning = 'Avertissement : la sauvegarde avant import a échoué. Importation poursuivie. (' . $e->getMessage() . ')';
        }

        try {
            $rapport = CsvHelper::import($csvPath, $this->produitModel());
        } catch (\Throwable $e) {
            $this->render('admin/importsviews', [
                'rapport' => ['error' => 'Erreur lors de l’import CSV : ' . $e->getMessage()],
                'importFiles' => $importFiles,
            ]);
            return;
        }

        if ($backupWarning !== null) {
            $rapport['warnings'][] = $backupWarning;
        }

        if (!empty($rapport['imported'])) {
            ImportHistoryHelper::markImported(
                $csvPath,
                $serverFile !== '' ? $serverFile : ($file['name'] ?? basename($csvPath))
            );
        }

        $this->render('admin/importsviews', ['rapport' => $rapport, 'importFiles' => $importFiles]);
    }
}
