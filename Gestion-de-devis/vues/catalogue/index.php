<?php
/**
 * vues/catalogue/index.php
 * Vue principale du devis instantané.
 * ⚠️  Ne pas inclure le layout ici — c'est le controller qui s'en charge.
 */
?>

<section class="banner" aria-label="Bannière Estimation">
    <div class="banner__overlay"></div>
    <div class="banner__content">
        <h1 class="banner__title">Devis instantané</h1>
    </div>
</section>

<section class="instant-quote py-5 text-center" id="instant-quote">
    <div class="container">

        <?php include __DIR__ . '/../steps/steps.php'; ?>
        <?php include __DIR__ . '/../steps/appareil.php'; ?>
        <?php include __DIR__ . '/../steps/modele.php'; ?>

    </div>
</section>

<section id="catalogue-section" class="catalogue-section d-none py-5"
         aria-live="polite" aria-label="Catalogue de réparations">
    <div class="container">

        <?php include __DIR__ . '/../steps/probleme.php'; ?>
        <?php include __DIR__ . '/../steps/reparation.php'; ?>
        <?php include __DIR__ . '/../steps/panier.php'; ?>
        <?php include __DIR__ . '/../steps/coordonne.php'; ?>

    </div>
</section>

<button id="btn-top" class="btn-floating" aria-label="Remonter en haut" title="Remonter en haut">
    ↑
</button>
