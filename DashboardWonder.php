<?php
require_once 'db/get_wonders.php';
session_start(); // Zorg dat de sessie gestart is
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
    <div class="register-container" style="background:transparent;box-shadow:none;">
        <h2>🌍 Alle Wereldwonderen</h2>
    </div>
    <div class="wonders-grid">
        <?php if (empty($wereldwonderen)): ?>
            <p style="color:#1e3c72;font-weight:bold;">Er zijn nog geen wereldwonderen toegevoegd.</p>
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
                    <a href="wereldwonder.php?id=<?php echo $wonder['id']; ?>">Bekijk</a>

                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'onderzoeker'): ?>
                        <a href="edit_wonder.php?id=<?= $wonder['id'] ?>" style="background:#1e88e5;color:#fff;margin-left:10px;">Bewerk</a>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'beheerder'): ?>
                        <a href="delete_wonder.php?id=<?= $wonder['id'] ?>" onclick="return confirm('Weet je zeker dat je dit wereldwonder wilt verwijderen?');" style="background:#e53935;color:#fff;margin-left:10px;">Verwijder</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
