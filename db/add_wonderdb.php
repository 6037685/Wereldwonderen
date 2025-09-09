<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'onderzoeker') {
    header("Location: ../login.php");
    exit;
}

require_once 'conectie.php';

$name = trim($_POST['name'] ?? '');
$location = trim($_POST['location'] ?? '');
$description = trim($_POST['description'] ?? '');
$photoPath = '';

if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $uploadDir = '../uploads/';
    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, $allowedExts)) {
        $newName = uniqid('wonder_', true) . '.' . $ext;
        $photoPath = $uploadDir . $newName;
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath)) {
            header("Location: ../add_wonder.php?error=upload");
            exit;
        }
        // Opslaan zonder ../ voor database
        $photoPath = 'uploads/' . $newName;
    } else {
        header("Location: ../add_wonder.php?error=type");
        exit;
    }
}

if ($name && $location && $description) {
    $stmt = $pdo->prepare("INSERT INTO wonders (name, location, description, photo, added_by) VALUES (:name, :location, :description, :photo, :added_by)");
    $stmt->execute([
        ':name' => $name,
        ':location' => $location,
        ':description' => $description,
        ':photo' => $photoPath,
        ':added_by' => $_SESSION['user_id']
    ]);

    $wonder_id = $pdo->lastInsertId();
    header("Location: ../wereldwonder.php?id=" . $wonder_id);
    exit;
} else {
    header("Location: ../add_wonder.php?error=1");
    exit;
}