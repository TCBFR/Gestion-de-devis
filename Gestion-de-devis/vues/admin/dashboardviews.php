<?php
$devisList = $devisList ?? [];
$databaseError = $databaseError ?? null;
$totalDevis = count($devisList);

$caTotal = array_sum(array_column($devisList, 'total_ttc'));
$panierMoyen = $totalDevis > 0 ? $caTotal / $totalDevis : 0;

$last7 = [];
for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days"));
    $last7[$day] = ['label' => date('d/m', strtotime($day)), 'count' => 0, 'ca' => 0];
}
foreach ($devisList as $d) {
    $day = substr($d['created_at'], 0, 10);
    if (isset($last7[$day])) {
        $last7[$day]['count']++;
        $last7[$day]['ca'] += $d['total_ttc'];
    }
}

$chartLabels = json_encode(array_values(array_column($last7, 'label')));
$chartCounts = json_encode(array_values(array_column($last7, 'count')));
$chartCa     = json_encode(array_values(array_map(fn($d) => round($d['ca'], 2), $last7)));

$produitCount = [];
foreach ($devisList as $d) {
    foreach ($d['produits'] ?? [] as $p) {
        $nom = $p['nom'] ?? 'Inconnu';
        $produitCount[$nom] = ($produitCount[$nom] ?? 0) + 1;
    }
}
arsort($produitCount);
$top5 = array_slice($produitCount, 0, 5, true);
$maxCount = $top5 ? max($top5) : 1;

$derniers = array_slice($devisList, 0, 5);
$aujourdhui = date('Y-m-d');
$debutSemaine = date('Y-m-d', strtotime('monday this week'));
$debutMois = date('Y-m-01');
$countAujourdhui = $countSemaine = $countMois = 0;
foreach ($devisList as $d) {
    $day = substr($d['created_at'], 0, 10);
    if ($day === $aujourdhui) {
        $countAujourdhui++;
    }
    if ($day >= $debutSemaine) {
        $countSemaine++;
    }
    if ($day >= $debutMois) {
        $countMois++;
    }
}
?>
<link rel="stylesheet" href="/assets/css/dashboard.css">

<?php if ($databaseError): ?>
    <div class="db-alert"><?= htmlspecialchars($databaseError) ?></div>
<?php endif; ?>

<?php require VIEWS_DIR . '/admin/dashboardsummary.php'; ?>
<?php require VIEWS_DIR . '/admin/dashboardgraph.php'; ?>
<?php require VIEWS_DIR . '/admin/dashboardrecent.php'; ?>
<?php require VIEWS_DIR . '/admin/dashboardpassword.php'; ?>

<?php if ($totalDevis > 0): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
window.dashboardChartData = {
    labels: <?= $chartLabels ?>,
    counts: <?= $chartCounts ?>,
    ca:     <?= $chartCa ?>,
};
</script>
<script src="/assets/js/dashboard.js"></script>
<?php endif; ?>
