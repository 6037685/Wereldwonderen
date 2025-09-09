<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'onderzoeker') {
    header("Location: login.php");
    exit;
}
require_once 'db/conectie.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM wonders WHERE id = ?");
$stmt->execute([$id]);
$wonder = $stmt->fetch();

if (!$wonder) {
    echo "Wereldwonder niet gevonden!";
    exit;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Wereldwonder bewerken</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'header.php'; ?>
<main>
    <h2>Wereldwonder bewerken</h2>
    <form action="db/update_wonderdb.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $wonder['id'] ?>">
        <label>Naam: <input type="text" name="name" value="<?= htmlspecialchars($wonder['name']) ?>" required></label><br>
        <label>Locatie: <input type="text" name="location" value="<?= htmlspecialchars($wonder['location']) ?>" required></label><br>
        <label>Beschrijving:<br>
            <textarea name="description" required><?= htmlspecialchars($wonder['description']) ?></textarea>
        </label><br>
        <label>Foto: <input type="file" name="photo"></label><br>
        <?php if ($wonder['photo']): ?>
            <img src="<?= htmlspecialchars($wonder['photo']) ?>" alt="Foto" style="max-width:150px;"><br>
        <?php endif; ?>
        <button type="submit">Opslaan</button>
    </form>
</main>
<?php include 'footer.php'; ?>
</body>
</html>