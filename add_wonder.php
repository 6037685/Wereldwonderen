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
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($wonder['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="location">Locatie</label>
                <input type="text" id="location" name="location" value="<?= htmlspecialchars($wonder['location'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="type">Type</label>
                <select id="type" name="type" required>
                    <option value="">Selecteer type</option>
                    <option value="klassiek" <?= (isset($wonder['type']) && $wonder['type']=='klassiek')?'selected':''; ?>>Klassiek</option>
                    <option value="modern" <?= (isset($wonder['type']) && $wonder['type']=='modern')?'selected':''; ?>>Modern</option>
                </select>
            </div>
            <div class="form-group">
                <label for="continent">Werelddeel</label>
                <select id="continent" name="continent" required>
                    <option value="">Selecteer werelddeel</option>
                    <option value="Europa" <?= (isset($wonder['continent']) && $wonder['continent']=='Europa')?'selected':''; ?>>Europa</option>
                    <option value="Azië" <?= (isset($wonder['continent']) && $wonder['continent']=='Azië')?'selected':''; ?>>Azië</option>
                    <option value="Afrika" <?= (isset($wonder['continent']) && $wonder['continent']=='Afrika')?'selected':''; ?>>Afrika</option>
                    <option value="Amerika" <?= (isset($wonder['continent']) && $wonder['continent']=='Amerika')?'selected':''; ?>>Amerika</option>
                    <option value="Oceanië" <?= (isset($wonder['continent']) && $wonder['continent']=='Oceanië')?'selected':''; ?>>Oceanië</option>
                </select>
            </div>
            <div class="form-group">
                <label for="bestaat_nog">Bestaat nog</label>
                <select id="bestaat_nog" name="bestaat_nog" required>
                    <option value="1" <?= (isset($wonder['bestaat_nog']) && $wonder['bestaat_nog']==1)?'selected':''; ?>>Ja</option>
                    <option value="0" <?= (isset($wonder['bestaat_nog']) && $wonder['bestaat_nog']==0)?'selected':''; ?>>Nee</option>
                </select>
            </div>
            <!-- Voeg hier andere velden toe zoals beschrijving, foto, etc. -->
            <button type="submit" class="btn-register">Opslaan</button>
        </form>
    </div>
</main>
<?php include 'footer.php'; ?>
</body>
</html>