<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_role = $_SESSION['user_role'] ?? '';
if (!in_array($user_role, ['onderzoeker','archivaris'])) {
    die("Je hebt geen rechten om dit wereldwonder te bewerken.");
}

require_once 'conectie.php';

$id = intval($_POST['id'] ?? 0);
$name = $_POST['name'] ?? '';
$location = $_POST['location'] ?? '';
$description = $_POST['description'] ?? '';

// Huidige foto ophalen
$stmt = $pdo->prepare("SELECT photo FROM wonders WHERE id = ?");
$stmt->execute([$id]);
$current = $stmt->fetch();
if (!$current) die("Wereldwonder niet gevonden!");

$photoPath = $current['photo'];

// Foto uploaden indien aanwezig
if (!empty($_FILES['photo']['name'])) {
    $uploadDir = "../uploads/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName = time() . "_" . basename($_FILES['photo']['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
        $photoPath = "uploads/" . $fileName;
    }
}

// Alleen archivaris kan bouwjaar/historische info aanpassen
$bouwjaar = $user_role === 'archivaris' ? $_POST['bouwjaar'] ?? null : null;
$historische_info = $user_role === 'archivaris' ? $_POST['historische_info'] ?? null : null;

// Update query
if ($user_role === 'archivaris') {
    $stmt = $pdo->prepare("UPDATE wonders SET name=?, location=?, description=?, photo=?, photo_approved=0, bouwjaar=?, historische_info=? WHERE id=?");
    $stmt->execute([$name, $location, $description, $photoPath, $bouwjaar, $historische_info, $id]);
} else {
    $stmt = $pdo->prepare("UPDATE wonders SET name=?, location=?, description=?, photo=?, photo_approved=0 WHERE id=?");
    $stmt->execute([$name, $location, $description, $photoPath, $id]);
}

header("Location: ../wereldwonder.php?id=$id");
exit;
