<?php
require_once 'db/get_wonders.php';
session_start();
require_once 'db/filterr.php';
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
    <form method="get" class="filter-bar" style="margin-bottom:32px;display:flex;flex-wrap:wrap;gap:16px;align-items:center;justify-content:center;">
        <input type="text" name="zoek" placeholder="Zoek op naam..." value="<?= htmlspecialchars($zoek) ?>" style="padding:8px;border-radius:6px;border:1px solid #ccc;">
        <select name="type" style="padding:8px;border-radius:6px;">
            <option value="">Type</option>
            <option value="klassiek" <?= $type=='klassiek'?'selected':''; ?>>Klassiek</option>
            <option value="modern" <?= $type=='modern'?'selected':''; ?>>Modern</option>
        </select>
        <select name="werelddeel" style="padding:8px;border-radius:6px;">
            <option value="">Werelddeel</option>
            <option value="Europa" <?= $werelddeel=='Europa'?'selected':''; ?>>Europa</option>
            <option value="Azië" <?= $werelddeel=='Azië'?'selected':''; ?>>Azië</option>
            <option value="Afrika" <?= $werelddeel=='Afrika'?'selected':''; ?>>Afrika</option>
            <option value="Amerika" <?= $werelddeel=='Amerika'?'selected':''; ?>>Amerika</option>
            <option value="Oceanië" <?= $werelddeel=='Oceanië'?'selected':''; ?>>Oceanië</option>
        </select>
        <select name="bestaat" style="padding:8px;border-radius:6px;">
            <option value="">Bestaat nog?</option>
            <option value="1" <?= $bestaat==='1'?'selected':''; ?>>Ja</option>
            <option value="0" <?= $bestaat==='0'?'selected':''; ?>>Nee</option>
        </select>
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'archivaris'): ?>
            <select name="sort" style="padding:8px;border-radius:6px;">
                <option value="">Sorteren</option>
                <option value="jaar" <?= $sort=='jaar'?'selected':''; ?>>Jaartal</option>
                <option value="naam" <?= $sort=='naam'?'selected':''; ?>>Alfabet</option>
            </select>
        <?php endif; ?>
        <button type="submit" style="padding:8px 18px;border-radius:6px;background:#ff9800;color:#222;font-weight:bold;border:none;">Filter</button>
    </form>
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
