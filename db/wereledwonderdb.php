<?php 

require_once 'db/conectie.php';
session_start();

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "Geen wereldwonder opgegeven!";
    exit;
}

// Haal wereldwonder op
$stmt = $pdo->prepare("SELECT w.*, u.name AS added_by_name FROM wonders w LEFT JOIN users u ON w.added_by = u.id WHERE w.id = :id");
$stmt->execute([':id' => $id]);
$wonder = $stmt->fetch();

if (!$wonder) {
    echo "Wereldwonder niet gevonden!";
    exit;
}

if (isset($_GET['approve']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'redacteur') {
    $stmt = $pdo->prepare("UPDATE wonders SET photo_approved = 1 WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $wonder['photo_approved'] = 1; 
}

$foto = (!empty($wonder['photo']) && $wonder['photo_approved'] == 1)
        ? htmlspecialchars($wonder['photo'])
        : "images/default.jpg";
?>


?>