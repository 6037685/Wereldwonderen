<?php
require_once 'db/conectie.php';
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'redacteur') {
    die("Geen toegang");
}

if (!isset($_GET['id'])) {
    die("Geen foto geselecteerd");
}

$id = (int)$_GET['id'];

// Foto goedkeuren
$stmt = $pdo->prepare("UPDATE wonders SET photo_approved = 1 WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit;
