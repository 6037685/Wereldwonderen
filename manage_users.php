<?php
session_start();
require_once 'db/gebruikersToevoegen.php';
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Gebruikers beheren</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'header.php'; ?>
<main>
    <div class="dashboard-container">
        <h2>👥 Gebruikers beheren</h2>
        <?php if (!empty($msg)): ?>
            <div style="color:green;font-weight:bold;"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <h3>Nieuwe gebruiker toevoegen</h3>
        <form method="post" style="margin-bottom:30px;">
            <input type="hidden" name="add_user" value="1">
            <label>Naam: <input type="text" name="name" required></label>
            <label>Email: <input type="email" name="email" required></label>
            <label>Wachtwoord: <input type="password" name="password" required></label>
            <label>Rol:
                <select name="role" required>
                    <option value="bezoeker">Bezoeker</option>
                    <option value="onderzoeker">Onderzoeker</option>
                    <option value="redacteur">Redacteur</option>
                    <option value="archivaris">Archivaris</option>
                    <option value="beheerder">Beheerder</option>
                </select>
            </label>
            <button type="submit">Toevoegen</button>
        </form>

        <h3>Bestaande gebruikers</h3>
        <table border="1" cellpadding="8" style="width:100%;max-width:700px;">
            <tr>
                <th>Naam</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Rol wijzigen</th>
            </tr>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="change_role" value="1">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <select name="new_role">
                            <option value="bezoeker" <?= $user['role']=='bezoeker'?'selected':''; ?>>Bezoeker</option>
                            <option value="onderzoeker" <?= $user['role']=='onderzoeker'?'selected':''; ?>>Onderzoeker</option>
                            <option value="redacteur" <?= $user['role']=='redacteur'?'selected':''; ?>>Redacteur</option>
                            <option value="archivaris" <?= $user['role']=='archivaris'?'selected':''; ?>>Archivaris</option>
                            <option value="beheerder" <?= $user['role']=='beheerder'?'selected':''; ?>>Beheerder</option>
                        </select>
                        <button type="submit">Opslaan</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</main>
<?php include 'footer.php'; ?>
</body>
</html>