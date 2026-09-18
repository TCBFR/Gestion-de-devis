# BiscaPhone Tarifs

Application PHP complète pour la gestion de devis de réparation de téléphonie mobile.

Elle couvre :
- import de tarifs produits depuis un fichier CSV fournisseur,
- administration sécurisée,
- sauvegarde de base de données,
- export CSV,
- génération de devis PDF,
- stockage des devis en JSON.

---

## Table des matières

1. [Vue d'ensemble](#vue-densemble)
2. [Fonctionnalités clés](#fonctionnalites-cles)
3. [Architecture](#architecture)
4. [Prérequis](#prerequis)
5. [Installation](#installation)
6. [Configuration](#configuration)
7. [Flux utilisateur](#flux-utilisateur)
8. [Admin et sécurité](#admin-et-securite)
9. [Import CSV](#import-csv)
10. [Export et backup](#export-et-backup)
11. [PDF](#pdf)
12. [Scripts CLI](#scripts-cli)
13. [Déploiement](#deploiement)
14. [Structure du projet](#structure-du-projet)
15. [Maintenance](#maintenance)
16. [Dépannage](#depannage)

---

## Vue d'ensemble

Cette application permet de gérer un catalogue de pièces, de générer des devis clients et de produire des PDF de devis.

Le back-office admin gère l'import CSV, les backups, les exports et la consultation des devis.

---

## Fonctionnalités clés

- Catalogue produits avec tarifs fournisseurs et prix client TTC
- Import CSV flexible pour fichiers fournisseur
- Backup MySQL automatique et manuel
- Export CSV du catalogue et des devis
- Stockage des devis en JSON et affichage du détail
- Génération PDF via `wkhtmltopdf`
- Authentification admin sécurisée

---

## Architecture

- Backend : PHP 8.x
- Base de données : MySQL
- Modèles : `model/`
- Contrôleurs : `controllers/`
- Logiciels partagés : `helpers/`
- Vues HTML : `vues/`
- Point d’entrée web : `public/index.php`
- Stockage local : `storage/`
- Scripts CLI : `scripts/`
- Config serveur : `deploy/`

---

## Prérequis

- PHP 8.x avec PDO MySQL
- MySQL ou MariaDB
- `mysqldump` accessible depuis le PATH ou défini par `MYSQLDUMP_BIN`
- `wkhtmltopdf`
- Serveur web Apache ou Nginx
- Permissions écriture sur `storage/`

---

## Installation

1. Copier le fichier de configuration :
   ```bash
   cp .env.example .env
   ```
2. Modifier `.env` avec les accès MySQL et les informations vendeur.
3. Vérifier que les dossiers `storage/` et `storage/imports/` existent et sont accessibles.
4. Configurer le serveur web pour pointer vers `public/`.
5. Lancer l'application.

---

## Configuration

### Fichier `.env`

Utilisez `.env.example` et ajustez les variables suivantes :

- `APP_DEBUG`
- `DOMAIN`
- `DB_HOST`
- `DB_PORT`
- `DB_NAME`
- `DB_USER`
- `DB_PASSWORD`
- `MYSQLDUMP_BIN`
- `WKHTMLTOPDF_BIN`
- `VENDEUR_*`

### Chargement

`config/config.php` charge les variables `.env` et les expose via `config('...')`.

### Répertoires importants

- `storage/devis/` : devis JSON
- `storage/backups/` : fichiers SQL de sauvegarde
- `storage/imports/` : CSV larges à importer

---

## Flux utilisateur

### Interface publique

L'application publique propose :
- choix de marque/modèle
- sélection de pièces
- saisie des coordonnées client
- génération de devis au format PDF

### Interface admin

L'admin utilise les pages suivantes :
- `/admin/login` : connexion
- `/admin` : tableau de bord
- `/admin/import` : import des tarifs
- `/admin/export-csv` : export catalogue
- `/admin/devis` : liste des devis
- `/admin/devis/{id}/export-csv` : export d'un devis

---

## Admin et sécurité

### Login

- Le login se fait via `/admin/login`.
- Mot de passe stocké en bcrypt dans `storage/admin_password.hash`.
- Si le fichier n'existe pas, le mot de passe par défaut `admin123` est généré.
- Après connexion, une session est maintenue pour accéder aux pages admin.

### Contrôle d'accès

- Toute route admin vérifie la session.
- Sans authentification, la redirection renvoie vers `/admin/login`.

---

## Import CSV

### Fonctionnement

- Le formulaire `/admin/import` accepte un fichier CSV.
- Le fichier est traité ligne par ligne.
- Les colonnes sont reconnues même avec accents, espaces et casse.
- Le séparateur décimal peut être `.` ou `,`.
- Les colonnes `EAN-13` et `URL` sont ignorées.
- Une sauvegarde MySQL est créée avant tout import.

### Format attendu

Colonnes importantes :

- `Type d'appareil`
- `Marque`
- `Modèle`
- `Nom`
- `Prix-HT`
- `Image`

### Mode volumineux

- Déposer le fichier dans `storage/imports/`
- Sélectionner le fichier depuis l'interface admin
- Le fichier est importé sans passer par un upload PHP direct

---

## Export et backup

### Export catalogue

- Route : `/admin/export-csv`
- Format : CSV contenant Type, Marque, Modèle, Nom, Prix HT, Prix TTC et Image

### Export devis

- Route globale : `/admin/devis-export-csv`
- Route unique : `/admin/devis/{id}/export-csv`
- Le CSV contient les produits et les totaux calculés

### Backup

- Backup manuel depuis l’interface admin
- Script CLI :
  ```bash
  php scripts/backupdatabase.php
  ```
- Les fichiers SQL sont stockés dans `storage/backups/`

---

## PDF

### Génération

- `helpers/pdfhelper.php` utilise `wkhtmltopdf`
- Le template de devis est dans `vues/devis/pdftemplate.php`
- Le PDF est généré en HTML puis converti

### Configuration

- Binaire configuré via `WKHTMLTOPDF_BIN` dans `.env`

---

## Scripts CLI

### Import gros CSV

```bash
php scripts/importcsv.php /chemin/vers/fichier.csv
```

### Backup base

```bash
php scripts/backupdatabase.php
```

---

## Déploiement

### Nginx

Exemple dans `deploy/nginx-biscaphone.conf.example`.

- `root` doit pointer vers `public/`
- PHP doit être servi par PHP-FPM
- Les actifs statiques doivent être servis directement

### Systemd

Exemple dans `deploy/biscaphone.service.example`.

---

## Structure du projet

- `public/` : point d’entrée et assets
- `config/` : configuration et router
- `controllers/` : logique de contrôle
- `helpers/` : fonctions de support (CSV, PDF, backup)
- `model/` : accès aux données
- `vues/` : gabarits HTML
- `storage/` : données, backups, imports
- `scripts/` : utilitaires CLI
- `deploy/` : configurations serveur exemples

---

## Maintenance

### Sauvegarde quotidienne

Cron recommandé :

```cron
0 3 * * * /usr/bin/php /chemin/vers/projet/scripts/backupdatabase.php >> /var/log/biscaphone_backup.log 2>&1
```

### Contrôles périodiques

- Vérifier les permissions du dossier `storage/`
- S’assurer que `wkhtmltopdf` fonctionne
- Valider que MySQL est accessible

---

## Dépannage

### Import CSV

- Vérifier `upload_max_filesize` et `post_max_size`
- Utiliser `storage/imports/` pour fichiers volumineux
- Contrôler le rapport d’import affiché

### Backup

- Vérifier `mysqldump` et les droits sur `storage/backups/`
- Vérifier la configuration MySQL dans `.env`

### PDF

- Vérifier `WKHTMLTOPDF_BIN`
- Tester `wkhtmltopdf` en ligne de commande

---

## Notes finales

- Les devis sont stockés en JSON dans `storage/devis/`.
- L’import CSV ignore les colonnes non utilisées.
- L’authentification admin est assurée par session et bcrypt.
- La documentation de déploiement est fournie dans `deploy/`.
