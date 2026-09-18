<?php
namespace Biscaphone\Controllers;

class AdminController extends AdminBaseController
{
    public function loginForm(): void
    {
        $this->renderLogin(['error' => null]);
    }

    public function login(): void
    {
        $password     = $_POST['password'] ?? '';
        $passwordFile = config('admin.password_file');

        if (!file_exists($passwordFile)) {
            file_put_contents($passwordFile, password_hash('admin123', PASSWORD_BCRYPT));
        }

        $hash = trim(file_get_contents($passwordFile));
        if (password_verify($password, $hash)) {
            $_SESSION['admin_logged'] = true;
            header('Location: /admin');
            return;
        }

        $this->renderLogin(['error' => 'Mot de passe incorrect']);
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: /admin/login');
    }

    public function dashboard(): void
    {
        $this->requireAuth();
        $databaseError = null;

        try {
            $devisList = $this->devisModel()->all();
        } catch (\Throwable) {
            $devisList = [];
            $databaseError = 'Base de données indisponible : les devis ne peuvent pas être chargés.';
        }

        $this->render('admin/dashboardviews', compact('devisList', 'databaseError'));
    }

    private function renderLogin(array $data = []): void
    {
        $viewFile = VIEWS_DIR . '/admin/loginviews.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("Vue login introuvable : $viewFile");
        }

        extract($data);
        require $viewFile;
    }
}
