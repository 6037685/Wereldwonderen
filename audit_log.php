<?php
session_start();
require 'db/conectie.php'; 
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'beheerder') {
    header("Location: login.php");
    exit;
}

// Haal log op
$stmt = $pdo->query("
    SELECT l.*, u.name AS user_name 
    FROM audit_log l 
    LEFT JOIN users u ON l.user_id = u.id 
    ORDER BY l.created_at DESC
");
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Wijzigingslog - Adventure</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<main>
    <div class="dashboard-container">
        <h2>📜 Wijzigingslog Wereldwonderen</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Gebruiker</th>
                    <th>Actie</th>
                    <th>Target Type</th>
                    <th>Target ID</th>
                    <th>Oude waarde</th>
                    <th>Nieuwe waarde</th>
                    <th>Tijd</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= $log['id'] ?? '' ?></td>
                        <td><?= htmlspecialchars($log['user_name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($log['action'] ?? '') ?></td>
                        <td><?= htmlspecialchars($log['target_type'] ?? '') ?></td>
                        <td><?= $log['target_id'] ?? '' ?></td>
                        <td>
                            <pre><?= htmlspecialchars($log['old_value'] ?? '') ?></pre>
                        </td>
                        <td>
                            <pre><?= htmlspecialchars($log['new_value'] ?? '') ?></pre>
                        </td>
                        <td><?= $log['created_at'] ?? '' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
