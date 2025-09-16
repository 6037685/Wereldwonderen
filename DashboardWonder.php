<?php
require_once 'db/get_wonders.php';
session_start();
require_once 'db/filterr.php';

// Statistiek: aantal wonderen per werelddeel (alleen voor beheerder)
$werelddeel_stats = [];
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'beheerder') {
    $stmt = $pdo->query("SELECT continent, COUNT(*) AS aantal FROM wonders GROUP BY continent");
    $werelddeel_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
}



// CSV export
if (isset($_GET['export']) && $_SESSION['user_role'] === 'beheerder') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="wereldwonderen.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID','Naam','Locatie','Beschrijving','Tags','Bouwjaar','Historische info','Type','Continent','Bestaat nog','Toegevoegd door','Gemaakt op','Views']);

    $stmt = $pdo->query("SELECT w.*, u.name AS added_by_name FROM wonders w LEFT JOIN users u ON w.added_by = u.id ORDER BY w.id ASC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $row['id'],
            $row['name'],
            $row['location'],
            $row['description'],
            $row['tags'],
            $row['bouwjaar'],
            $row['historische_info'],
            $row['type'],
            $row['continent'],
            $row['bestaat_nog'],
            $row['added_by_name'],
            $row['created_at'],
            $row['views']
        ]);
    }
    fclose($output);
    exit;
}

// Meest bekeken wonderen
$stmtTop = $pdo->query("SELECT * FROM wonders ORDER BY views DESC LIMIT 5");
$topViewed = $stmtTop->fetchAll();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Wereldwonderen Overzicht</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'header.php'; ?>
<main>
<div class="dashboard-container">

    <div class="dashboard-main">
        <!-- Filterformulier -->
        <form method="get" class="filter-bar" style="margin-bottom:32px; display:flex; flex-wrap:wrap; gap:16px; align-items:center; justify-content:center;">
            <input type="text" name="zoek" placeholder="Zoek op naam..." value="<?= htmlspecialchars($zoek) ?>" style="padding:8px; border-radius:6px; border:1px solid #ccc;">
            <select name="type" style="padding:8px; border-radius:6px;">
                <option value="">Type</option>
                <option value="klassiek" <?= $type=='klassiek'?'selected':''; ?>>Klassiek</option>
                <option value="modern" <?= $type=='modern'?'selected':''; ?>>Modern</option>
            </select>
            <select name="werelddeel" style="padding:8px; border-radius:6px;">
                <option value="">Werelddeel</option>
                <option value="Europa" <?= $werelddeel=='Europa'?'selected':''; ?>>Europa</option>
                <option value="Azië" <?= $werelddeel=='Azië'?'selected':''; ?>>Azië</option>
                <option value="Afrika" <?= $werelddeel=='Afrika'?'selected':''; ?>>Afrika</option>
                <option value="Amerika" <?= $werelddeel=='Amerika'?'selected':''; ?>>Amerika</option>
                <option value="Oceanië" <?= $werelddeel=='Oceanië'?'selected':''; ?>>Oceanië</option>
            </select>
            <select name="bestaat" style="padding:8px; border-radius:6px;">
                <option value="">Bestaat nog?</option>
                <option value="1" <?= $bestaat==='1'?'selected':''; ?>>Ja</option>
                <option value="0" <?= $bestaat==='0'?'selected':''; ?>>Nee</option>
            </select>
            <button type="submit" style="padding:8px 18px; border-radius:6px; background:#ff9800; color:#222; font-weight:bold; border:none;">Filter</button>
        </form>

        <!-- Exportknoppen -->
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'beheerder'): ?>
            <a href="?export=1" style="padding:8px 12px; background:#43a047; color:#fff; border-radius:6px; text-decoration:none; margin-right:10px;">📄 Exporteer naar CSV</a>
            <form method="post" action="export_wonders_word.php" style="display:inline-block;">
                <button type="submit" style="padding:8px 18px; border-radius:6px; background:#4CAF50; color:#fff; font-weight:bold; border:none;">
                    📄 Exporteer naar Word
                </button>
            </form>
        <?php endif; ?>

        <!-- Onderzoeker exportknop -->
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'onderzoeker'): ?>
            <form method="post" action="export_wonders_word.php" style="margin-top:10px; margin-bottom:20px;">
                <button type="submit" style="padding:8px 18px; border-radius:6px; background:#4CAF50; color:#fff; font-weight:bold; border:none;">
                    📄 Download mijn werk
                </button>
            </form>
        <?php endif; ?>
        <h3>Meest bekeken wonderen</h3>
        <ul>
            <?php foreach($topViewed as $w): ?>
                <li><?= htmlspecialchars($w['name']) ?> (<?= $w['views'] ?> keer bekeken)</li>
            <?php endforeach; ?>
        </ul>

        <!-- Wereldwonderen grid -->
        <div class="wonders-grid">
            <?php if (empty($wereldwonderen)): ?>
                <p style="color:#1e3c72; font-weight:bold;">Er zijn nog geen wereldwonderen toegevoegd.</p>
            <?php else: ?>
<?php foreach ($wereldwonderen as $wonder): ?>
    <div class="wonder-card">
        <?php 
            $foto = (!empty($wonder['photo']) && $wonder['photo_approved'] == 1) 
                    ? htmlspecialchars($wonder['photo']) 
                    : "images/default.jpg"; 
        ?>
        <img src="<?php echo $foto; ?>" alt="Foto van <?php echo htmlspecialchars($wonder['name']); ?>">
        <h3><?php echo htmlspecialchars($wonder['name']); ?></h3>
        <div class="loc">📍 <?php echo htmlspecialchars($wonder['location']); ?></div>

        <!-- Bekijk link -->
        <a href="wereldwonder.php?id=<?php echo $wonder['id']; ?>" style="display:inline-block; margin-right:8px;">Bekijk</a>

        <!-- Alleen voor beheerders: Verwijderknop -->
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'beheerder'): ?>
            <form method="post" action="delete_wonder.php" onsubmit="return confirm('Weet je zeker dat je dit wereldwonder wilt verwijderen?');" style="display:inline-block; margin-top:8px;">
                <input type="hidden" name="wonder_id" value="<?= $wonder['id'] ?>">
                <button type="submit" style="padding:6px 12px; background:#e53935; color:#fff; border:none; border-radius:4px; cursor:pointer;">
                    Verwijder
                </button>
            </form>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

            <?php endif; ?>
        </div>
    </div>

    <!-- Statistiek voor beheerder -->
    <?php if (!empty($werelddeel_stats) && $_SESSION['user_role'] === 'beheerder'): ?>
    <div class="dashboard-stats">
        <h4>Wonderen per werelddeel</h4>
        <ul>
            <?php foreach ($werelddeel_stats as $stat): ?>
                <li><?= htmlspecialchars($stat['continent'] ?? 'Onbekend') ?>: <strong><?= htmlspecialchars($stat['aantal']) ?></strong></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

</div>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
