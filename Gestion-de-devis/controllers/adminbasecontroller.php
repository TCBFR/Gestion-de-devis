<?php
namespace Biscaphone\Controllers;

use Biscaphone\Models\DevisModel;
use Biscaphone\Models\ProduitModel;

abstract class AdminBaseController
{
    protected ?ProduitModel $produitModel = null;
    protected ?DevisModel $devisModel = null;

    public function __construct()
    {
        session_start();
    }

    protected function produitModel(): ProduitModel
    {
        return $this->produitModel ??= new ProduitModel();
    }

    protected function devisModel(): DevisModel
    {
        return $this->devisModel ??= new DevisModel();
    }

    protected function requireAuth(): void
    {
        if (empty($_SESSION['admin_logged'])) {
            header('Location: /admin/login');
            exit;
        }
    }

    protected function render(string $view, array $data = []): void
    {
        static $alreadyRendered = false;

        // Protection simple : si un rendu a déjà eu lieu durant cette requête,
        // on évite de ré-inclure la vue (prévention contre double affichage).
        if ($alreadyRendered) {
            // Journaliser l'appel secondaire pour faciliter le diagnostic.
            $logDir = __DIR__ . '/../storage/logs';
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0755, true);
            }
            $logFile = $logDir . '/render_debug.log';
            $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            $caller = $bt[1] ?? $bt[0] ?? [];
            $message = date('c') . " - Second render attempt detected\n";
            $message .= 'Caller: ' . ($caller['class'] ?? '') . ($caller['type'] ?? '') . ($caller['function'] ?? '') . "\n";
            $message .= "Backtrace:\n";
            foreach ($bt as $i => $frame) {
                $message .= "#$i " . ($frame['class'] ?? '') . ($frame['type'] ?? '') . ($frame['function'] ?? '') . ' called at ' . ($frame['file'] ?? 'unknown') . ':' . ($frame['line'] ?? '0') . "\n";
            }
            @file_put_contents($logFile, $message . "\n", FILE_APPEND);
            return;
        }

        $viewFile = VIEWS_DIR . '/' . $view . '.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("Vue introuvable : $viewFile");
        }

        extract($data);
        $alreadyRendered = true;
        require VIEWS_DIR . '/layouts/admin.php';
    }
}
