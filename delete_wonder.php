<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'beheerder') {
    header('Location: index.php');
    exit;
}

if (isset($_GET['id'])) {
    require_once 'db/conectie.php'; 
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("DELETE FROM wonders WHERE id = ?");
    $stmt->execute([$id]);
}
header('Location: ../index.php');
exit;