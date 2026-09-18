<?php
// Vue Instant Quote — synchronisée avec le stepper de index.php
// Ce fichier est inclus quand on accède directement à /instant-quote
// Le stepper et les panels sont déjà dans index.php ;
// ce script gère uniquement le pré-remplissage via ?type= dans l'URL.
?>
<script>
/* ----------------------------------------------------------------
   Pré-remplissage depuis l'URL (?type=android, etc.)
   Si l'URL contient ?type=..., simule un clic sur la carte
   correspondante pour déclencher le passage au step 2.
---------------------------------------------------------------- */
document.addEventListener('DOMContentLoaded', () => {
    try {
        const params  = new URLSearchParams(window.location.search);
        const preType = params.get('type');
        if (!preType) return;

        // Trouver la carte correspondante et simuler le clic
        const card = document.querySelector(`.device-card[data-type="${CSS.escape(preType)}"]`);
        if (card) {
            card.click();
        }
    } catch (e) {
        // ignore — navigateur trop ancien
    }
});
</script>