<?php
require_once 'db/conectie.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'redacteur') {
    die("Geen toegang!");
}

// Foto goedkeuren via GET
if (isset($_GET['approve_id'])) {
    $approveId = (int)$_GET['approve_id'];
    $stmt = $pdo->prepare("UPDATE wonders SET photo_approved = 1 WHERE id = :id");
    $stmt->execute([':id' => $approveId]);
    header("Location: redacteur_overzicht.php?approved=1");
    exit;
}

// Locatie goedkeuren of afkeuren
if (isset($_GET['loc_id']) && isset($_GET['action'])) {
    $locId = (int)$_GET['loc_id'];
    $action = $_GET['action'];

    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE wonders SET location_approved = 1 WHERE id = :id");
        $stmt->execute([':id' => $locId]);
    } elseif ($action === 'reject') {
        $stmt = $pdo->prepare("UPDATE wonders SET location_approved = 0 WHERE id = :id");
        $stmt->execute([':id' => $locId]);
    }

    header("Location: redacteur_overzicht.php");
    exit;
}

// Laatst toegevoegde wonderen
$stmtRecent = $pdo->query("SELECT w.id, w.name, w.created_at, u.name AS added_by_name
                           FROM wonders w
                           LEFT JOIN users u ON w.added_by = u.id
                           ORDER BY w.created_at DESC
                           LIMIT 10");
$recentWonders = $stmtRecent->fetchAll();

// Haal bijdragen op die nog niet zijn goedgekeurd (foto)
$stmt = $pdo->query("SELECT w.id, w.name, w.location, w.photo, u.name AS added_by_name 
                     FROM wonders w
                     LEFT JOIN users u ON w.added_by = u.id
                     WHERE w.photo IS NOT NULL AND w.photo_approved = 0");
$pendingPhotos = $stmt->fetchAll();

// Haal alle bijdragen op voor GPS-check
$stmt2 = $pdo->query("SELECT w.id, w.name, w.location, w.latitude, w.longitude, w.location_approved, u.name AS added_by_name 
                      FROM wonders w
                      LEFT JOIN users u ON w.added_by = u.id
                      ORDER BY w.name ASC");
$allWonders = $stmt2->fetchAll();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Redacteur Overzicht</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .pending-item, .wonder-item, .recent-item { 
            border:1px solid #ddd; 
            padding:15px; 
            margin-bottom:15px; 
            border-radius:8px;
        }
        .btn-approve { background:#43a047; color:#fff; padding:8px 12px; border-radius:5px; text-decoration:none; margin-right:10px; }
        .btn-reject { background:#e53935; color:#fff; padding:8px 12px; border-radius:5px; text-decoration:none; margin-right:10px; }
        .btn-view { background:#0277bd; color:#fff; padding:8px 12px; border-radius:5px; text-decoration:none; }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<main>
<div class="container">

    <!-- Laatst toegevoegde wonderen -->
    <h2>Laatst toegevoegde wonderen</h2>
    <?php if(empty($recentWonders)): ?>
        <p>Geen wonderen gevonden.</p>
    <?php else: ?>
        <?php foreach($recentWonders as $w): ?>
            <div class="recent-item">
                <strong><?= htmlspecialchars($w['name']); ?></strong><br>
                <small>Toegevoegd op: <?= date('d-m-Y H:i', strtotime($w['created_at'])); ?> | Toegevoegd door: <?= htmlspecialchars($w['added_by_name']); ?></small><br>
                <a href="wereldwonder.php?id=<?= $w['id']; ?>" class="btn-view">Bekijk detail</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <hr>

    <!-- Bijdragen die nog goedgekeurd moeten worden -->
    <h2>Bijdragen die nog goedgekeurd moeten worden (foto's)</h2>
    <?php if (isset($_GET['approved'])): ?>
        <p style="color:green;">✅ Bijdrage is goedgekeurd!</p>
    <?php endif; ?>
    <?php if (empty($pendingPhotos)): ?>
        <p>✅ Er zijn momenteel geen bijdragen die goedgekeurd moeten worden.</p>
    <?php else: ?>
        <?php foreach ($pendingPhotos as $item): ?>
            <div class="pending-item">
                <strong><?= htmlspecialchars($item['name']); ?></strong> 
                (<?= htmlspecialchars($item['location']); ?>)<br>
                Toegevoegd door: <?= htmlspecialchars($item['added_by_name']); ?><br><br>

                <?php if (!empty($item['photo'])): ?>
                    <img src="<?= htmlspecialchars($item['photo']); ?>" alt="Foto" style="max-width:200px;"><br><br>
                <?php endif; ?>

                <a href="redacteur_overzicht.php?approve_id=<?= $item['id']; ?>" class="btn-approve">✅ Direct goedkeuren</a>
                <a href="wereldwonder.php?id=<?= $item['id']; ?>" class="btn-view">👁 Bekijk detail</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <hr>

    <!-- Controleer locaties (GPS) -->
    <h2>Controleer locaties (GPS)</h2>
    <?php if(empty($allWonders)): ?>
        <p>Geen wonderen gevonden.</p>
    <?php else: ?>
        <?php foreach($allWonders as $wonder): ?>
            <div class="wonder-item">
                <strong><?= htmlspecialchars($wonder['name']); ?></strong> 
                (Toegevoegd door: <?= htmlspecialchars($wonder['added_by_name']); ?>)<br>
                Locatie: <?= htmlspecialchars($wonder['location'] ?? 'Niet ingevuld'); ?><br>
                GPS: <?= !empty($wonder['latitude']) && !empty($wonder['longitude']) ? htmlspecialchars($wonder['latitude'] . ', ' . $wonder['longitude']) : 'Niet ingevuld'; ?><br><br>

                <a href="wereldwonder.php?id=<?= $wonder['id']; ?>" class="btn-view">👁 Bekijk detail / kaart</a>

                <?php if(!empty($wonder['latitude']) && !empty($wonder['longitude'])): ?>
                    <?php if($wonder['location_approved'] === null): ?>
                        <a href="redacteur_overzicht.php?loc_id=<?= $wonder['id']; ?>&action=approve" class="btn-approve">✅ Locatie goedkeuren</a>
                        <a href="redacteur_overzicht.php?loc_id=<?= $wonder['id']; ?>&action=reject" class="btn-reject">❌ Locatie afkeuren</a>
                    <?php elseif($wonder['location_approved'] == 1): ?>
                        <span style="color:green;">✅ Locatie goedgekeurd</span>
                    <?php else: ?>
                        <span style="color:red;">❌ Locatie afgekeurd</span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <br>
    <a href="DashboardWonder.php" class="btn-back">← Terug naar dashboard</a>
</div>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
