<?php
session_start();
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'onderzoeker') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
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
                <label for="type">Type</label>
                <select id="type" name="type" required>
                    <option value="">Selecteer type</option>
                    <option value="klassiek">Klassiek</option>
                    <option value="modern">Modern</option>
                </select>
            </div>
            <div class="form-group">
                <label for="continent">Werelddeel</label>
                <select id="continent" name="continent" required>
                    <option value="">Selecteer werelddeel</option>
                    <option value="Europa">Europa</option>
                    <option value="Azië">Azië</option>
                    <option value="Afrika">Afrika</option>
                    <option value="Amerika">Amerika</option>
                    <option value="Oceanië">Oceanië</option>
                </select>
            </div>
            <div class="form-group">
                <label for="bestaat_nog">Bestaat nog?</label>
                <select id="bestaat_nog" name="bestaat_nog" required>
                    <option value="1">Ja</option>
                    <option value="0">Nee</option>
                </select>
            </div>
            <div class="form-group">
                <label for="photo">Foto</label>
                <input type="file" id="photo" name="photo" accept="image/*">
            </div>
            <button type="submit" class="btn-register">Toevoegen</button>
        </form>
    </div>
</main>
<?php include 'footer.php'; ?>
</body>
</html>