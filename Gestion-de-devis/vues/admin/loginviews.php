<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiscaPhone — Administration</title>
    <link rel="stylesheet" href="/assets/css/login.css">
</head>
<body>
<div class="wrapper">
    <div class="header">
        <div class="header-logo">📱</div>
        <span class="header-name">BiscaPhone</span>
        <div class="header-sep"></div>
        <span class="header-admin">Administration</span>
    </div>

    <div class="card">
        <div class="card-title">Connexion</div>
        <p class="card-subtitle">Accès réservé à l'interface d'administration.</p>

        <?php if (!empty($error)): ?>
            <div class="alert"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/admin/login">
            <div class="field">
                <label for="password">Mot de passe</label>
                  <input type="text"
                       id="password"
                       name="password"
                      value="admin123"
                       autofocus
                       required
                       placeholder="Entrez votre mot de passe">
            </div>
            <button type="submit">Se connecter</button>
        </form>

        <div class="card-footer">
            tarifs.biscaphone.fr &mdash; <a href="https://biscaphone.fr">biscaphone.fr</a>
        </div>
    </div>
</div>
</body>
</html>
