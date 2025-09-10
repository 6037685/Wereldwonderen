<?php
require_once 'db/conectie.php';
session_start();

$id = $_GET['id'] ?? null;
if (!$id) die("Geen wereldwonder opgegeven!");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['user_role'] === 'archivaris') {
    $bouwjaar = $_POST['bouwjaar'] ?? null;
    $historische_info = $_POST['historische_info'] ?? null;
    $stmt = $pdo->prepare("UPDATE wonders SET bouwjaar = :bouwjaar, historische_info = :historische_info WHERE id = :id");
    $stmt->execute([
        ':bouwjaar' => $bouwjaar,
        ':historische_info' => $historische_info,
        ':id' => $id
    ]);
}

$stmt = $pdo->prepare("SELECT w.*, u.name AS added_by_name FROM wonders w LEFT JOIN users u ON w.added_by = u.id WHERE w.id = :id");
$stmt->execute([':id' => $id]);
$wonder = $stmt->fetch();
if (!$wonder) die("Wereldwonder niet gevonden!");

if (isset($_GET['approve']) && $_SESSION['user_role'] === 'redacteur') {
    $stmt = $pdo->prepare("UPDATE wonders SET photo_approved = 1 WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $wonder['photo_approved'] = 1;
}

$foto = (!empty($wonder['photo']) && $wonder['photo_approved'] == 1)
        ? htmlspecialchars($wonder['photo'])
        : "images/default.jpg";
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($wonder['name']); ?> - Wereldwonder</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'header.php'; ?>
<main>
<div class="wonder-detail-container">
    <h2><?= htmlspecialchars($wonder['name']); ?></h2>
    <div class="meta">
        📍 <?= htmlspecialchars($wonder['location']); ?><br>
        Toegevoegd door: <?= htmlspecialchars($wonder['added_by_name']); ?>
    </div>

    <img src="<?= $foto ?>" alt="Foto van <?= htmlspecialchars($wonder['name']); ?>">

    <div class="desc">
        <?= nl2br(htmlspecialchars($wonder['description'])); ?>
    </div>

    <?php if (!empty($wonder['bouwjaar'])): ?>
        <p>🏛 Bouwjaar: <?= htmlspecialchars($wonder['bouwjaar']); ?></p>
    <?php endif; ?>
    <?php if (!empty($wonder['historische_info'])): ?>
        <p><?= nl2br(htmlspecialchars($wonder['historische_info'])); ?></p>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'redacteur' && !empty($wonder['photo']) && $wonder['photo_approved'] == 0): ?>
        <a href="wereldwonder.php?id=<?= $wonder['id'] ?>&approve=1" class="btn-approve" style="background:#43a047;color:#fff;padding:10px;border-radius:5px;text-decoration:none;">
            ✅ Foto goedkeuren
        </a>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'archivaris'): ?>
        <hr>
        <h3>Bouwjaar & Historische info toevoegen/bewerken</h3>
        <form method="post">
            <label>Bouwjaar:<br>
                <input type="text" name="bouwjaar" value="<?= htmlspecialchars($wonder['bouwjaar'] ?? '') ?>">
            </label><br><br>
            <label>Historische info:<br>
                <textarea name="historische_info" rows="5" cols="50"><?= htmlspecialchars($wonder['historische_info'] ?? '') ?></textarea>
            </label><br><br>
            <button type="submit">Opslaan</button>
        </form>
    <?php endif; ?>

    <br><br>
    <a href="DashboardWonder.php" class="btn-back">← Terug naar overzicht</a>
</div>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
