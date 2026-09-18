<?php
/**
 * src/helpers/clientvalidator.php
 * --------------------------------
 * Validation simple des coordonnées client côté serveur.
 */

namespace Biscaphone\Helpers;

class ClientValidator
{
    public static function validate(array $post): array
    {
        $errors = [];
        $nom       = trim($post['nom'] ?? '');
        $telephone = trim($post['telephone'] ?? '');
        $email     = trim($post['email'] ?? '');

        if ($nom === '') {
            $errors['nom'] = 'Le nom est obligatoire';
        }
        if ($telephone === '') {
            $errors['telephone'] = 'Le téléphone est obligatoire';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email invalide';
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        return [
            'nom'       => htmlspecialchars($nom),
            'telephone' => htmlspecialchars($telephone),
            'email'     => htmlspecialchars($email),
            'adresse'   => [
                'rue'   => htmlspecialchars(trim($post['adresse_rue'] ?? '')),
                'cp'    => htmlspecialchars(trim($post['adresse_cp'] ?? '')),
                'ville' => htmlspecialchars(trim($post['adresse_ville'] ?? '')),
            ],
        ];
    }
}
