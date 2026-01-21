<?php

$pdo = bd_connect();

$stmt = $pdo->query(<<<'SQL'
    SELECT
        SUM(amour) AS total_love_points,
        COUNT(*) FILTER (WHERE lastconnect >= CURRENT_TIMESTAMP - INTERVAL '5 minutes') AS last_5_min,
        COUNT(*) FILTER (WHERE lastconnect >= CURRENT_TIMESTAMP - INTERVAL '1 hour') AS last_hour,
        COUNT(*) FILTER (WHERE lastconnect >= CURRENT_TIMESTAMP - INTERVAL '12 hours') AS last_12h,
        COUNT(*) FILTER (WHERE lastconnect >= CURRENT_TIMESTAMP - INTERVAL '24 hours') AS last_24h,
        COUNT(*) FILTER (WHERE lastconnect >= CURRENT_TIMESTAMP - INTERVAL '48 hours') AS last_48h,
        COUNT(*) FILTER (WHERE lastconnect >= CURRENT_TIMESTAMP - INTERVAL '7 days') AS last_week,
        COUNT(*) FILTER (WHERE lastconnect >= CURRENT_TIMESTAMP - INTERVAL '30 days') AS last_month,
        COUNT(*) FILTER (WHERE lastconnect >= CURRENT_TIMESTAMP - INTERVAL '1 year') AS last_year
    FROM membres
SQL);
/**
 * @var array{
 *     total_love_points: int|null,
 *     last_5_min: int,
 *     last_hour: int,
 *     last_12h: int,
 *     last_24h: int,
 *     last_48h: int,
 *     last_week: int,
 *     last_month: int,
 *     last_year: int,
 * } $result
 */
$result = $stmt->fetch();
$totalLovePoints = $result['total_love_points'] ?? 0;
$connectedInLast5Minutes = $result['last_5_min'];
$connectedInLastHour = $result['last_hour'];
$connectedInLast12Hours = $result['last_12h'];
$connectedInLast24Hours = $result['last_24h'];
$connectedInLast48Hours = $result['last_48h'];
$connectedInLastWeek = $result['last_week'];
$connectedInLastMonth = $result['last_month'];
$connectedInLastYear = $result['last_year'];
?>
<h1>Statistiques</h1>
<span class="info">[ Statistiques à compter du 26 avril 2006 ]</span><br />
<br />
<br />
Nombre total de points d'amours disponibles dans le jeu : <?php echo formaterNombre($totalLovePoints); ?><br />
<br />
Nombre de points d'amours moyen par personne : <?php echo $connectedInLastYear > 0 ? formaterNombre($totalLovePoints / $connectedInLastYear) : 0; ?><br />
<br />
Nombre de membres connectés dans les dernières 5 minutes : <?php echo $connectedInLast5Minutes; ?><br />
<br />
Nombre de membres connectés dans les dernières 60 minutes : <?php echo $connectedInLastHour; ?><br />
<br />
Nombre de membres connectés dans les dernières 12 heures : <?php echo $connectedInLast12Hours; ?><br />
<br />
Nombre de membres connectés dans les dernières 24 heures : <?php echo $connectedInLast24Hours; ?><br />
<br />
Nombre de membres connectés dans les dernières 48 heures : <?php echo $connectedInLast48Hours; ?><br />
<br />
Nombre de membres connectés dans les derniers 7 jours : <?php echo $connectedInLastWeek; ?><br />
<br />
Nombre de membres connectés dans les derniers 30 jours : <?php echo $connectedInLastMonth; ?><br />
<br />
Nombre de membres connectés depuis un an : <?php echo $connectedInLastYear; ?><br />
<br />
