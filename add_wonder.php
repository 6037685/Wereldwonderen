<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'onderzoeker') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Nieuw Wereldwonder Toevoegen</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'header.php'; ?>
<main>
    <div class="register-container">
        <h2>🌍 Nieuw Wereldwonder</h2>
        <form action="db/add_wonderdb.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Naam wereldwonder</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="location">Locatie</label>
                <input type="text" id="location" name="location" required>
            </div>
            <div class="form-group">
                <label for="description">Beschrijving</label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="photo">Foto</label>
                <input type="file" id="photo" name="photo" accept="image/*" required>
            </div>
            <button type="submit" class="btn-register">Toevoegen</button>
        </form>
    </div>
</main>
<?php include 'footer.php'; ?>
</body>
</html>