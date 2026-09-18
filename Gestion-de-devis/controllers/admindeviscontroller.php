<?php
namespace Biscaphone\Controllers;

use Biscaphone\Helpers\DevisCsvExportHelper;

class AdminDevisController extends AdminBaseController
{
    public function devisList(): void
    {
        $this->requireAuth();
        try {
            $devisList = $this->devisModel()->all();
            $error = null;
        } catch (\Throwable) {
            $devisList = [];
            $error = 'Base de données indisponible : les devis ne peuvent pas être chargés.';
        }

        $this->render('devis/devislistviews', compact('devisList', 'error'));
    }

    public function devisShow(array $params): void
    {
        $this->requireAuth();
        try {
            $devis = $this->devisModel()->find($params['id']);
        } catch (\Throwable) {
            http_response_code(503);
            echo '<p>Base de données indisponible.</p>';
            return;
        }

        if (!$devis) {
            http_response_code(404);
            echo '<p>Devis introuvable.</p>';
            return;
        }

        $this->render('admin/devisdetailviews', compact('devis'));
    }

    public function devisExportCsv(): void
    {
        $this->requireAuth();
        try {
            DevisCsvExportHelper::export($this->devisModel()->all());
        } catch (\Throwable) {
            http_response_code(503);
            echo '<p>Base de données indisponible : export impossible.</p>';
        }
    }

    public function devisExportSingle(array $params): void
    {
        $this->requireAuth();
        try {
            $devis = $this->devisModel()->find($params['id']);
        } catch (\Throwable) {
            http_response_code(503);
            echo '<p>Base de données indisponible.</p>';
            return;
        }

        if (!$devis) {
            http_response_code(404);
            echo '<p>Devis introuvable.</p>';
            return;
        }

        DevisCsvExportHelper::exportSingle($devis);
    }
}
