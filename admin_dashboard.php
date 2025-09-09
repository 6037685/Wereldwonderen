<?php
session_start();

if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'beheerder') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Adventure</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'header.php'; ?>

<main>
    <div class="dashboard-container">
        <h2>👑 Admin Dashboard</h2>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
        <ul>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="DashboardWonder.php">Wereldwonderen Beheren</a></li>
            <li><a href="index.php">Go to Homepage</a></li>
            <li><a href="db/logout.php">Logout</a></li>
        </ul>
    </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>