<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <nav>
        <a href="index.php">Home</a>
        <a href="DashboardWonder.php">Wereldwonderen</a>

        <?php if (empty($_SESSION['user_id'])): ?>
            <a href="login.php">Login</a>
            <a href="register.php">Registreren</a>
        <?php else: ?>
            
            <!-- Alleen voor beheerders -->
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'beheerder'): ?>
                <a href="admin_dashboard.php">Admin Dashboard</a>
                <a href="audit_log.php">Wijzigingslog</a>
            <?php endif; ?>

            <!-- Alleen voor onderzoekers -->
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'onderzoeker'): ?>
                <a href="add_wonder.php">Nieuw Wereldwonder</a>
            <?php endif; ?>

            <!-- Alleen voor redacteurs -->
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'redacteur'): ?>
                <a href="redacteur_overzicht.php">Goed te keuren</a>
            <?php endif; ?>

            <a href="db/logout.php">Logout</a>
        <?php endif; ?>
    </nav>
</header>
