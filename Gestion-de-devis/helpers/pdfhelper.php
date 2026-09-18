<?php
/**
 * src/helpers/pdfhelper.php
 * --------------------------
 * Génère un devis PDF à partir d'un tableau de données.
 * Utilise wkhtmltopdf : le HTML du template est converti en PDF côté serveur.
 * Le fichier est écrit dans /tmp puis supprimé après envoi.
 */

namespace Biscaphone\Helpers;

class PdfHelper
{
    /**
     * Génère le PDF d'un devis et retourne son chemin temporaire.
     * L'appelant est responsable de supprimer le fichier après usage (unlink).
     */
    public static function generate(array $devis): string
    {
        $wkhtmltopdf = config('wkhtmltopdf.bin');
        $tmpHtml     = tempnam(sys_get_temp_dir(), 'devis_') . '.html';
        $tmpPdf      = tempnam(sys_get_temp_dir(), 'devis_') . '.pdf';

        // Rend le template HTML du devis dans une variable
        ob_start();
        $vendeur = config('vendeur');
        $config  = [
            'validite_jours' => config('devis.validite_jours'),
        ];
        require VIEWS_DIR . '/devis/pdftemplate.php';
        $html = ob_get_clean();

        // Écrit le HTML dans un fichier temporaire
        file_put_contents($tmpHtml, $html);

        // Appel à wkhtmltopdf avec les options de mise en page
        $cmd = escapeshellcmd($wkhtmltopdf)
            . ' --page-size A4'
            . ' --margin-top 15mm'
            . ' --margin-bottom 15mm'
            . ' --margin-left 15mm'
            . ' --margin-right 15mm'
            . ' --encoding UTF-8'
            . ' --enable-local-file-access'
            . ' --quiet'
            . ' ' . escapeshellarg($tmpHtml)
            . ' ' . escapeshellarg($tmpPdf);

        exec($cmd, $output, $returnCode);

        // Nettoyage du fichier HTML temporaire
        unlink($tmpHtml);

        if ($returnCode !== 0 || !file_exists($tmpPdf)) {
            throw new \RuntimeException('Erreur lors de la génération du PDF (wkhtmltopdf).');
        }

        return $tmpPdf;
    }
}