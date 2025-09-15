<?php
require_once 'db/conectie.php';
session_start();

// Veilig maken van session variabelen
$user_role = $_SESSION['user_role'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

$id = $_GET['id'] ?? null;
if (!$id) die("Geen wereldwonder opgegeven!");

// Data ophalen
$stmt = $pdo->prepare("SELECT w.*, u.name AS added_by_name 
                       FROM wonders w 
                       LEFT JOIN users u ON w.added_by = u.id 
                       WHERE w.id = :id");
$stmt->execute([':id' => $id]);
$wonder = $stmt->fetch();
if (!$wonder) die("Wereldwonder niet gevonden!");

// POST verwerken
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ARCHIVARIS: bouwjaar, historische info, document, GPS, bestaat_nog
    if ($user_role === 'archivaris') {
        $bouwjaar = $_POST['bouwjaar'] ?? null;
        $bouwjaar = $bouwjaar === '' ? null : (int)$bouwjaar;

        $historische_info = $_POST['historische_info'] ?? null;

        $latitude = $_POST['latitude'] ?? null;
        $latitude = $latitude === '' ? null : (float)$latitude;

        $longitude = $_POST['longitude'] ?? null;
        $longitude = $longitude === '' ? null : (float)$longitude;

        $bestaat_nog = $_POST['bestaat_nog'] ?? null;
        $bestaat_nog = $bestaat_nog === '' ? null : (int)$bestaat_nog;

        $document_path = $wonder['document'];
        if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['pdf','doc','docx'])) {
                $document_path = 'uploads/' . uniqid('doc_') . '.' . $ext;
                move_uploaded_file($_FILES['document']['tmp_name'], $document_path);
            }
        }

        $stmt = $pdo->prepare("UPDATE wonders 
                               SET bouwjaar=:bouwjaar, historische_info=:historische_info, 
                                   document=:document, latitude=:latitude, longitude=:longitude,
                                   bestaat_nog=:bestaat_nog
                               WHERE id=:id");
        $stmt->execute([
            ':bouwjaar' => $bouwjaar,
            ':historische_info' => $historische_info,
            ':document' => $document_path,
            ':latitude' => $latitude,
            ':longitude' => $longitude,
            ':bestaat_nog' => $bestaat_nog,
            ':id' => $id
        ]);

        $wonder['bouwjaar'] = $bouwjaar;
        $wonder['historische_info'] = $historische_info;
        $wonder['document'] = $document_path;
        $wonder['latitude'] = $latitude;
        $wonder['longitude'] = $longitude;
        $wonder['bestaat_nog'] = $bestaat_nog;
    }

    // ONDERZOEKER: eigen bijdrage + locatie + korte beschrijving
    if ($user_role === 'onderzoeker' && $wonder['added_by'] == $user_id) {
        $description = $_POST['description'] ?? $wonder['description'];
        $short_description = $_POST['short_description'] ?? $wonder['short_description'];
        $location = $_POST['location'] ?? $wonder['location'];

        $latitude = $_POST['latitude'] ?? null;
        $latitude = $latitude === '' ? null : (float)$latitude;

        $longitude = $_POST['longitude'] ?? null;
        $longitude = $longitude === '' ? null : (float)$longitude;

        $stmt = $pdo->prepare("UPDATE wonders 
                               SET description=:description, short_description=:short_description, location=:location, latitude=:latitude, longitude=:longitude
                               WHERE id=:id AND added_by=:user_id");
        $stmt->execute([
            ':description' => $description,
            ':short_description' => $short_description,
            ':location' => $location,
            ':latitude' => $latitude,
            ':longitude' => $longitude,
            ':id' => $id,
            ':user_id' => $user_id
        ]);

        $wonder['description'] = $description;
        $wonder['short_description'] = $short_description;
        $wonder['location'] = $location;
        $wonder['latitude'] = $latitude;
        $wonder['longitude'] = $longitude;
    }
}

// REDACTEUR: foto goedkeuren
if (isset($_GET['approve']) && $user_role === 'redacteur') {
    $stmt = $pdo->prepare("UPDATE wonders SET photo_approved=1 WHERE id=:id");
    $stmt->execute([':id' => $id]);
    $wonder['photo_approved'] = 1;
}

// Foto
$foto = (!empty($wonder['photo']) && $wonder['photo_approved'] == 1) ? htmlspecialchars($wonder['photo']) : "images/default.jpg";

// Haal goedgekeurde wonderen met GPS op voor kaart
$wondersMapStmt = $pdo->query("SELECT id,name,short_description,latitude,longitude FROM wonders WHERE latitude IS NOT NULL AND longitude IS NOT NULL AND location_approved=1");
$wondersMap = $wondersMapStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<title><?= $id ? htmlspecialchars($wonder['name']) : 'Kaart van Wereldwonderen'; ?></title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
</head>
<body>
<?php include 'header.php'; ?>
<main>
<div class="wonder-detail-container">

<?php if($id && $wonder): ?>
    <h2><?= htmlspecialchars($wonder['name']); ?></h2>
    <div class="meta">
        📍 <?= htmlspecialchars($wonder['location']); ?><br>
        Toegevoegd door: <?= htmlspecialchars($wonder['added_by_name']); ?>
    </div>
    <img src="<?= $foto ?>" alt="Foto van <?= htmlspecialchars($wonder['name']); ?>">

    <?php if(!empty($wonder['short_description'])): ?>
        <p><strong>Korte beschrijving:</strong> <?= nl2br(htmlspecialchars($wonder['short_description'])); ?></p>
    <?php endif; ?>

    <?php if(!empty($wonder['bouwjaar'])): ?>
        <p>🏛 Bouwjaar: <?= htmlspecialchars($wonder['bouwjaar']); ?></p>
    <?php endif; ?>

    <?php if(!empty($wonder['historische_info'])): ?>
        <p><?= nl2br(htmlspecialchars($wonder['historische_info'])); ?></p>
    <?php endif; ?>

    <?php if(!empty($wonder['document'])): ?>
        <p>📄 <a href="<?= htmlspecialchars($wonder['document']); ?>" target="_blank">Download reisverslag/document</a></p>
    <?php endif; ?>

    <?php if(isset($wonder['bestaat_nog'])): ?>
        <p>🟢 Bestaat nog: <?= $wonder['bestaat_nog'] ? 'Ja' : 'Nee'; ?></p>
    <?php endif; ?>

    <?php if(!empty($wonder['latitude']) && !empty($wonder['longitude'])): ?>
        <?php if($wonder['location_approved']==1): ?>
            <p>🌍 GPS: <?= htmlspecialchars($wonder['latitude']); ?>, <?= htmlspecialchars($wonder['longitude']); ?></p>
            <div id="map" style="height:300px;"></div>
        <?php else: ?>
            <p style="color:red;">❌ Deze GPS-locatie is door de redacteur afgekeurd.</p>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Formulier voor Archivaris -->
    <?php if($user_role==='archivaris'): ?>
        <hr>
        <h3>Bewerk gegevens</h3>
        <form method="post" enctype="multipart/form-data">
            <label>Bouwjaar:<br>
                <input type="text" name="bouwjaar" value="<?= htmlspecialchars($wonder['bouwjaar'] ?? '') ?>"></label><br><br>
            <label>Historische info:<br>
                <textarea name="historische_info" rows="5" cols="50"><?= htmlspecialchars($wonder['historische_info'] ?? '') ?></textarea></label><br><br>
            <label>GPS Breedtegraad:<br>
                <input type="text" name="latitude" value="<?= htmlspecialchars($wonder['latitude'] ?? '') ?>"></label><br><br>
            <label>GPS Lengtegraad:<br>
                <input type="text" name="longitude" value="<?= htmlspecialchars($wonder['longitude'] ?? '') ?>"></label><br><br>
            <label>Bestaat nog:<br>
                <select name="bestaat_nog">
                    <option value="">-- Kies --</option>
                    <option value="1" <?= $wonder['bestaat_nog']==1?'selected':''; ?>>Ja</option>
                    <option value="0" <?= $wonder['bestaat_nog']==0?'selected':''; ?>>Nee</option>
                </select>
            </label><br><br>
            <label>Reisverslag/document:<br>
                <input type="file" name="document" accept=".pdf,.doc,.docx"></label><br><br>
            <button type="submit">Opslaan</button>
        </form>
    <?php endif; ?>

    <!-- Formulier voor Onderzoekers -->
    <?php if ($user_role === 'onderzoeker' && $wonder['added_by'] == $user_id): ?>
        <hr>
        <h3>Eigen bijdrage aanpassen</h3>
        <form method="post">
            <label>Korte beschrijving:<br>
                <input type="text" name="short_description" value="<?= htmlspecialchars($wonder['short_description'] ?? '') ?>" maxlength="255" style="width:100%;"></label><br><br>
            <label>Beschrijving:<br>
                <textarea name="description" rows="5" cols="50"><?= htmlspecialchars($wonder['description'] ?? '') ?></textarea></label><br><br>
            <label>Locatie (plaatsnaam):<br>
                <input type="text" name="location" value="<?= htmlspecialchars($wonder['location'] ?? '') ?>"></label><br><br>
            <label>GPS Breedtegraad:<br>
                <input type="text" name="latitude" value="<?= htmlspecialchars($wonder['latitude'] ?? '') ?>" placeholder="52.3676"></label><br><br>
            <label>GPS Lengtegraad:<br>
                <input type="text" name="longitude" value="<?= htmlspecialchars($wonder['longitude'] ?? '') ?>" placeholder="4.9041"></label><br><br>
            <button type="submit">Opslaan</button>
        </form>
    <?php endif; ?>

<?php endif; ?>

<hr>
<h2>Kaart van alle goedgekeurde wonderen</h2>
<div id="mapAll" style="height:500px;"></div>

</div>
</main>
<?php include 'footer.php'; ?>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<?php if($id && $wonder && !empty($wonder['latitude']) && !empty($wonder['longitude']) && $wonder['location_approved']==1): ?>
<script>
var map = L.map('map').setView([<?= $wonder['latitude']; ?>, <?= $wonder['longitude']; ?>], 6);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'&copy; OpenStreetMap' }).addTo(map);
L.marker([<?= $wonder['latitude']; ?>, <?= $wonder['longitude']; ?>]).addTo(map)
    .bindPopup("<strong><?= addslashes($wonder['name']); ?></strong><br><?= addslashes($wonder['short_description'] ?? '') ?>");
</script>
<?php endif; ?>

<script>
// Interactieve kaart voor alle wonderen
var mapAll = L.map('mapAll').setView([52.3676, 4.9041], 6);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'&copy; OpenStreetMap' }).addTo(mapAll);

<?php foreach($wondersMap as $w): ?>
L.marker([<?= $w['latitude']; ?>, <?= $w['longitude']; ?>]).addTo(mapAll)
    .bindPopup("<strong><?= addslashes($w['name']); ?></strong><br><?= addslashes($w['short_description'] ?? '') ?><br><a href='wereldwonder.php?id=<?= $w['id']; ?>'>Meer lezen</a>");
<?php endforeach; ?>
</script>
</body>
</html>
