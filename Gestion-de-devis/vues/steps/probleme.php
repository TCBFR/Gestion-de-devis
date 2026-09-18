<?php
$reparations = [
    [
        'key' => 'ecran',
        'label' => 'Écran cassé',
        'aria' => 'Écran cassé',
        'svg' => '<svg viewBox="0 0 80 80" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><rect x="20" y="8" width="40" height="64" rx="6"/><line x1="34" y1="16" x2="46" y2="16"/><circle cx="40" cy="62" r="3" fill="currentColor" stroke="none"/><line x1="28" y1="30" x2="52" y2="50" stroke-width="3.5"/><line x1="52" y1="30" x2="28" y2="50" stroke-width="3.5"/></svg>',
    ],
    [
        'key' => 'vitre-arriere',
        'label' => 'Vitre arrière',
        'aria' => 'Vitre arrière',
        'svg' => '<svg viewBox="0 0 80 80" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><rect x="22" y="8" width="36" height="64" rx="6"/><circle cx="40" cy="18" r="3"/><line x1="36" y1="62" x2="44" y2="62"/></svg>',
    ],
    [
        'key' => 'batterie',
        'label' => 'Batterie',
        'aria' => 'Batterie',
        'svg' => '<svg viewBox="0 0 80 80" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><rect x="16" y="20" width="44" height="36" rx="6"/><line x1="60" y1="31" x2="66" y2="31"/><line x1="60" y1="49" x2="66" y2="49"/><line x1="63" y1="31" x2="63" y2="49"/><polyline points="38,28 32,40 42,40 36,52" stroke-width="3.5"/></svg>',
    ],
    [
        'key' => 'connecteur',
        'label' => 'Connecteur de charge',
        'aria' => 'Connecteur de charge',
        'svg' => '<svg viewBox="0 0 80 80" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><line x1="40" y1="12" x2="40" y2="36"/><rect x="24" y="36" width="32" height="20" rx="10"/><line x1="40" y1="56" x2="40" y2="68"/><line x1="30" y1="12" x2="30" y2="22"/><line x1="50" y1="12" x2="50" y2="22"/></svg>',
    ],
    [
        'key' => 'camera',
        'label' => 'Caméra',
        'aria' => 'Caméra',
        'svg' => '<svg viewBox="0 0 80 80" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 56V30a4 4 0 0 1 4-4h8l5-8h22l5 8h8a4 4 0 0 1 4 4v26a4 4 0 0 1-4 4H16a4 4 0 0 1-4-4z"/><circle cx="40" cy="42" r="10"/><circle cx="40" cy="42" r="5"/></svg>',
    ],
    [
        'key' => 'haut-parleur',
        'label' => 'Haut-parleur',
        'aria' => 'Haut-parleur',
        'svg' => '<svg viewBox="0 0 80 80" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polygon points="16,30 16,50 28,50 44,62 44,18 28,30"/><path d="M52 30 a14 14 0 0 1 0 20" stroke-linecap="round"/><path d="M58 22 a24 24 0 0 1 0 36" stroke-linecap="round"/></svg>',
    ],
    [
        'key' => 'bouton',
        'label' => 'Ne s\'allume plus',
        'aria' => 'Bouton Power',
        'svg' => '<svg viewBox="0 0 80 80" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="M26 22 a22 22 0 1 0 28 0"/><line x1="40" y1="12" x2="40" y2="42"/></svg>',
    ],
    [
        'key' => 'degat-eau',
        'label' => 'Dégât des eaux',
        'aria' => 'Dégât des eaux',
        'svg' => '<svg viewBox="0 0 80 80" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="M40 12 C40 12 18 38 18 50 a22 22 0 0 0 44 0 C62 38 40 12 40 12z"/><path d="M30 52 a12 12 0 0 0 10 8" stroke-width="3"/></svg>',
    ],
];
?>

<div id="repair-valider-wrap" style="text-align:center; margin-bottom:1.25rem; display:none;">
    <button id="btn-valider-reparations" style="background: var(--color-accent); color:#fff; border:none; padding:0.85rem 2.5rem; border-radius:14px; font-weight:700; font-size:1.05rem; cursor:pointer; font-family:var(--font-body); box-shadow:0 4px 18px rgba(0,0,100,0.13); transition:opacity 0.18s;">
        Valider ma réparation →
    </button>
</div>

<div id="repair-cards-grid" class="device-grid repair-grid mt-4 d-none" role="list" aria-label="Choisissez une réparation">
    <?php foreach ($reparations as $repair): ?>
        <button class="device-card repair-card" data-reparation="<?= $repair['key'] ?>" role="listitem" aria-label="<?= htmlspecialchars($repair['aria']) ?>">
            <span class="repair-icon" aria-hidden="true"><?= $repair['svg'] ?></span>
            <span class="device-card__label"><?= htmlspecialchars($repair['label']) ?></span>
        </button>
    <?php endforeach; ?>
</div>
