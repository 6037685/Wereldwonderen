<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'onderzoeker') {
    header("Location: ../login.php");
    exit;
}

require_once 'conectie.php';

$id = intval($_POST['id'] ?? 0);
$name = $_POST['name'] ?? '';
$location = $_POST['location'] ?? '';
$description = $_POST['description'] ?? '';

$stmt = $pdo->prepare("SELECT photo FROM wonders WHERE id = ?");
$stmt->execute([$id]);
$current = $stmt->fetch();

if (!$current) {
    die("Wereldwonder niet gevonden!");
}

$photoPath = $current['photo'];

if (!empty($_FILES['photo']['name'])) {
    $uploadDir = "../uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES['photo']['name']); 
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
        $photoPath = "uploads/" . $fileName; 
    }
}

$stmt = $pdo->prepare("UPDATE wonders SET name=?, location=?, description=?, photo=?, photo_approved=0 WHERE id=?");
$stmt->execute([$name, $location, $description, $photoPath, $id]);


header("Location: ../index.php");
exit;
