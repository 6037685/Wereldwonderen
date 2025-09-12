<?php 

require_once 'db/conectie.php';

if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'beheerder') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
    $role = $_POST['role'] ?? 'bezoeker';

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetchColumn() > 0) {
        $msg = "Dit e-mailadres is al in gebruik!";
    } elseif ($name && $email && $_POST['password']) {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $password,
            ':role' => $role
        ]);
        $msg = "Gebruiker toegevoegd!";
    } else {
        $msg = "Vul alle velden in.";
    }
}

// Rol wijzigen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['new_role'];
    $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
    $stmt->execute([
        ':role' => $new_role,
        ':id' => $user_id
    ]);
    $msg = "Rol aangepast!";
}

$stmt = $pdo->query("SELECT id, name, email, role FROM users ORDER BY name ASC");
$users = $stmt->fetchAll();
?>

