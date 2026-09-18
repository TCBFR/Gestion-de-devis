<?php
/**
 * src/Controllers/DevisController.php
 */

namespace Biscaphone\Controllers;

use Biscaphone\Helpers\ClientValidator;
use Biscaphone\Helpers\DevisCreator;
use Biscaphone\Helpers\PdfHelper;
use Biscaphone\Models\ProduitModel;
use Biscaphone\Models\DevisModel;

class DevisController
{
    private ?ProduitModel $produitModel = null;
    private ?DevisModel   $devisModel = null;

    private function produitModel(): ProduitModel
    {
        return $this->produitModel ??= new ProduitModel();
    }

    private function devisModel(): DevisModel
    {
        return $this->devisModel ??= new DevisModel();
    }

    public function create(): void
    {
        $client = ClientValidator::validate($_POST);
        if (isset($client['errors'])) {
            http_response_code(422);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($client);
            return;
        }

        $ids = json_decode($_POST['panier'] ?? '[]', true);
        if (!is_array($ids) || empty($ids)) {
            http_response_code(422);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['errors' => ['panier' => 'Le panier est vide']]);
            return;
        }

        try {
            $result = DevisCreator::create($ids, $client, $this->produitModel(), $this->devisModel());
        } catch (\InvalidArgumentException $e) {
            http_response_code(422);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['errors' => ['panier' => $e->getMessage()]]);
            return;
        } catch (\Throwable) {
            http_response_code(503);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Service devis indisponible : base de données ou génération PDF inaccessible.']);
            return;
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="devis-' . $result['devis']['id'] . '.pdf"');
        readfile($result['pdfPath']);
        unlink($result['pdfPath']);
    }

    public function show(array $params): void
    {
        try {
            $devis = $this->devisModel()->find($params['id']);
        } catch (\Throwable) {
            http_response_code(503);
            echo '<p>Service devis indisponible.</p>';
            return;
        }
        if (!$devis) {
            http_response_code(404);
            echo '<p>Devis introuvable.</p>';
            return;
        }

        $pdfPath = PdfHelper::generate($devis);
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="devis-' . $devis['id'] . '.pdf"');
        readfile($pdfPath);
        unlink($pdfPath);
    }
}
