<?php
namespace Biscaphone\Controllers;

use Biscaphone\Helpers\AdminBackupHelper;
use Biscaphone\Helpers\AdminCsvExportHelper;

class AdminMaintenanceController extends AdminBaseController
{
    public function backup(): void
    {
        $this->requireAuth();
        try {
            AdminBackupHelper::downloadBackup();
        } catch (\Throwable) {
            http_response_code(503);
            echo '<p>Base de données indisponible : sauvegarde impossible.</p>';
        }
    }

    public function exportCsv(): void
    {
        $this->requireAuth();
        try {
            AdminCsvExportHelper::export($this->produitModel()->getAllForAdmin());
        } catch (\Throwable) {
            http_response_code(503);
            echo '<p>Base de données indisponible : export impossible.</p>';
        }
    }

    public function changePassword(): void
    {
        $this->requireAuth();
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (strlen($new) < 8) {
            $this->render('admin/dashboardviews', ['error' => 'Le mot de passe doit faire au moins 8 caractères']);
            return;
        }
        if ($new !== $confirm) {
            $this->render('admin/dashboardviews', ['error' => 'Les mots de passe ne correspondent pas']);
            return;
        }

        file_put_contents(config('admin.password_file'), password_hash($new, PASSWORD_BCRYPT));
        header('Location: /admin?success=password');
    }
}
