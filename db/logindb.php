<?php
session_start();
require_once 'conectie.php'; 

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        if ($user['role'] === 'beheerder') {
            header("Location: ../admin_dashboard.php");
            exit;
        } else {
            header("Location: ../index.php");
            exit;
        }
    } else {
        $message = "Email of wachtwoord is onjuist!";
    }
}
?>