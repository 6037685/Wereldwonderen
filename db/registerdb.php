<?php
session_start();
require_once 'conectie.php'; 

try {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($password !== $confirm) {
        $_SESSION['register_error'] = "Wachtwoorden komen niet overeen!";
        header("Location: ../register.php");
        exit;
    }

    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
    $checkStmt->execute([':email' => $email]);
    if ($checkStmt->fetchColumn() > 0) {
        $_SESSION['register_error'] = "Deze gebruiker bestaat al! Kies een ander e-mailadres.";
        header("Location: ../register.php");
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':password' => $hashedPassword,
        ':role' => $role
    ]);


} catch (PDOException $e) {
    $_SESSION['register_error'] = "Error: " . $e->getMessage();
    header("Location: ../register.php");
    exit;
}

header("Location: ../index.php");
exit;
?>
