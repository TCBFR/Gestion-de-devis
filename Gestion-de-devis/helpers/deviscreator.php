<?php
/**
 * src/helpers/deviscreator.php
 * -----------------------------
 * Création d'un devis valide côté serveur.
 */

namespace Biscaphone\Helpers;

use Biscaphone\Models\ProduitModel;
use Biscaphone\Models\DevisModel;

class DevisCreator
{
    public static function create(array $ids, array $client, ProduitModel $produitModel, DevisModel $devisModel): array
    {
        $produits = self::resolveProducts($ids, $produitModel);
        if (empty($produits)) {
            throw new \InvalidArgumentException('Aucun produit valide dans le panier');
        }

        $lignesFixes = [
            ['nom' => 'Livraison express fournisseur', 'prix_client_ttc' => config('devis.livraison_ttc')],
            ['nom' => 'Main d\'œuvre Réparation (1 h)',  'prix_client_ttc' => config('devis.main_oeuvre_ttc')],
        ];

        $tousLesProduits = array_merge($produits, $lignesFixes);
        $totalTtc = array_sum(array_column($tousLesProduits, 'prix_client_ttc'));
        $devisId = $devisModel->save($client, $tousLesProduits, $totalTtc);

        $devis = $devisModel->find($devisId);
        if (!$devis) {
            throw new \RuntimeException('Impossible de retrouver le devis après sauvegarde.');
        }

        $pdfPath = PdfHelper::generate($devis);
        return ['devis' => $devis, 'pdfPath' => $pdfPath];
    }

    private static function resolveProducts(array $ids, ProduitModel $produitModel): array
    {
        $produits = [];

        foreach ($ids as $id) {
            $produit = $produitModel->getById((int) $id);
            if ($produit) {
                $produits[] = $produit;
            }
        }

        return $produits;
    }
}
