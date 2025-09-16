<?php
require_once 'conectie.php';

$stmt = $pdo->prepare("SELECT * FROM wonders WHERE id = ?");
$stmt->execute([$wonder_id]);
$oldWonder = $stmt->fetch(PDO::FETCH_ASSOC);

// Update wonder
$stmt = $pdo->prepare("UPDATE wonders SET name = :name, location = :location WHERE id = :id");
$stmt->execute([
    ':name' => $newName,
    ':location' => $newLocation,
    ':id' => $wonder_id
]);

// Voeg toe aan audit log
$stmtLog = $pdo->prepare("
    INSERT INTO audit_log (user_id, action, target_type, target_id, old_value, new_value)
    VALUES (:user_id, 'update', 'wonder', :target_id, :old_value, :new_value)
");
$stmtLog->execute([
    ':user_id' => $_SESSION['user_id'],
    ':target_id' => $wonder_id,
    ':old_value' => json_encode($oldWonder),
    ':new_value' => json_encode(['name' => $newName, 'location' => $newLocation])
]);
?>