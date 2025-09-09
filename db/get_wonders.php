<?php
require_once 'conectie.php';

$stmt = $pdo->query("SELECT * FROM wonders");
$wereldwonderen = $stmt->fetchAll();
?>