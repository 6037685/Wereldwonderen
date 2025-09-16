<?php
session_start();
require_once 'db/conectie.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'beheerder') {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['wonder_id'])) {
    $id = intval($_POST['wonder_id']);

    // Oude waarde voor audit log
    $stmt = $pdo->prepare("SELECT * FROM wonders WHERE id = ?");
    $stmt->execute([$id]);
    $oldWonder = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($oldWonder) {
        $stmt = $pdo->prepare("DELETE FROM wonders WHERE id = ?");
        $stmt->execute([$id]);

        // Audit log
        $stmtLog = $pdo->prepare("
            INSERT INTO audit_log (user_id, action, target_type, target_id, old_value, new_value)
            VALUES (:user_id, 'delete', 'wonder', :target_id, :old_value, NULL)
        ");
        $stmtLog->execute([
            ':user_id' => $_SESSION['user_id'],
            ':target_id' => $id,
            ':old_value' => json_encode($oldWonder)
        ]);
    }
}

header('Location: DashboardWonder.php');
exit;
