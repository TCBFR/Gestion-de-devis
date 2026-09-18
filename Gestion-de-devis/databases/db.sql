-- ============================================================
--  BiscaPhone — Schéma base de données
--  MySQL 8+ / MariaDB 10.5+
--  Tables : modele, produit
--  Triggers : calcul automatique de prix_client_ttc
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS biscaphone
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE biscaphone;

-- ============================================================
--  1. MODELE
--  Référentiel des appareils (type / marque / modèle).
--  Alimenté automatiquement lors de l'import CSV fournisseur.
-- ============================================================

CREATE TABLE IF NOT EXISTS modele (
    id            MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
    type_appareil VARCHAR(60)        NOT NULL COMMENT 'iphone | android | tablet | computer',
    marque        VARCHAR(80)        NOT NULL COMMENT 'Apple, Samsung…',
    modele        VARCHAR(120)       NOT NULL COMMENT 'iPhone 14 Pro, Galaxy S23…',
    actif         TINYINT(1)         NOT NULL DEFAULT 1,
    created_at    DATETIME           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_modele       (type_appareil, marque, modele),
    INDEX        idx_type_marque (type_appareil, marque)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Référentiel des modèles d''appareils';

-- ============================================================
--  2. PRODUIT
--
--  Règle de prix :
--    prix_client_ttc = ROUND(prix_fourn_ht × taux_marge × (1 + taux_tva), 2)
--    → recalculé automatiquement par trigger sur INSERT et UPDATE.
--
--  prix_fourn_ht est confidentiel : ne jamais l'exposer en front.
-- ============================================================

CREATE TABLE IF NOT EXISTS produit (
    id              INT UNSIGNED       NOT NULL AUTO_INCREMENT,
    modele_id       MEDIUMINT UNSIGNED NOT NULL,
    nom             VARCHAR(200)       NOT NULL COMMENT 'Libellé affiché au client',
    prix_fourn_ht   DECIMAL(10,2)      NOT NULL COMMENT 'Prix fournisseur HT — confidentiel',
    taux_tva        DECIMAL(5,4)       NOT NULL DEFAULT 0.2000 COMMENT '0.2000 = 20 %',
    taux_marge      DECIMAL(5,4)       NOT NULL DEFAULT 2.5000 COMMENT '2.5 = +150 %',
    prix_client_ttc DECIMAL(10,2)      NOT NULL COMMENT 'Calculé par trigger, affiché client',
    image_url       VARCHAR(500)           NULL DEFAULT NULL,
    actif           TINYINT(1)         NOT NULL DEFAULT 1,
    created_at      DATETIME           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_produit   (modele_id, nom),
    INDEX        idx_modele   (modele_id),

    CONSTRAINT fk_produit_modele
        FOREIGN KEY (modele_id) REFERENCES modele (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Catalogue produits avec prix calculé automatiquement';

-- ============================================================
--  3. TRIGGERS — calcul automatique de prix_client_ttc
-- ============================================================

DROP TRIGGER IF EXISTS trg_produit_prix_insert;

DELIMITER //
CREATE TRIGGER trg_produit_prix_insert
BEFORE INSERT ON produit
FOR EACH ROW
BEGIN
    SET NEW.prix_client_ttc = ROUND(
        NEW.prix_fourn_ht * NEW.taux_marge * (1 + NEW.taux_tva),
        2
    );
END //
DELIMITER ;


DROP TRIGGER IF EXISTS trg_produit_prix_update;

DELIMITER //
CREATE TRIGGER trg_produit_prix_update
BEFORE UPDATE ON produit
FOR EACH ROW
BEGIN
    IF  NEW.prix_fourn_ht <> OLD.prix_fourn_ht
     OR NEW.taux_marge    <> OLD.taux_marge
     OR NEW.taux_tva      <> OLD.taux_tva
    THEN
        SET NEW.prix_client_ttc = ROUND(
            NEW.prix_fourn_ht * NEW.taux_marge * (1 + NEW.taux_tva),
            2
        );
    END IF;
END //
DELIMITER ;

-- ============================================================
--  Fin du schéma
-- ============================================================

SET FOREIGN_KEY_CHECKS = 1;