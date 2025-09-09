<?php
session_start();
require_once 'conectie.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'beheerder') {
    header("Location: ../wereldwonderen.php");
    exit;
}

$wonder_id = $_POST['wonder_id'] ?? null;

if ($wonder_id) {
    // Optioneel: verwijder ook de foto van de server
    $stmt = $pdo->prepare("SELECT photo FROM wonders WHERE id = :id");
    $stmt->execute([':id' => $wonder_id]);
    $wonder = $stmt->fetch();
    if ($wonder && !empty($wonder['photo']) && file_exists("../" . $wonder['photo'])) {
        unlink("../" . $wonder['photo']);
    }

    // Verwijder het wereldwonder uit de database
    $stmt = $pdo->prepare("DELETE FROM wonders WHERE id = :id");
    $stmt->execute([':id' => $wonder_id]);
}

header("Location: ../wereldwonderen.php");
exit;
?>