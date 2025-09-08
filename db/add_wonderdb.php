<?php
session_start();
require_once 'conectie.php';

if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'onderzoeker') {
    header("Location: ../login.php");
    exit;
}

$name = $_POST['name'] ?? '';
$location = $_POST['location'] ?? '';
$description = $_POST['description'] ?? '';
$photoPath = '';

if ($name && $location && $description && isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/';
    $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('wonder_') . '.' . $ext;
    $targetFile = $uploadDir . $filename;

    // Optioneel: controleer bestandstype en grootte
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array(strtolower($ext), $allowed)) {
        header("Location: ../add_wonder.php?error=bestandstype");
        exit;
    }

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
        $photoPath = 'uploads/' . $filename;
    } else {
        header("Location: ../add_wonder.php?error=upload");
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO wonders (name, location, description, photo, added_by) VALUES (:name, :location, :description, :photo, :added_by)");
    $stmt->execute([
        ':name' => $name,
        ':location' => $location,
        ':description' => $description,
        ':photo' => $photoPath,
        ':added_by' => $_SESSION['user_id']
    ]);
    header("Location: ../wereldwonderen.php?success=1");
    exit;
} else {
    header("Location: ../add_wonder.php?error=1");
    exit;
}
?>