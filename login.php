<?php
session_start();
$message = '';
if (isset($_SESSION['login_message'])) {
    $message = $_SESSION['login_message'];
    unset($_SESSION['login_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Adventure</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<main>
  <div class="register-container">
    <h2>🌍 Login</h2>
    <?php if($message): ?>
      <p style="color:#ff6b6b; font-weight:bold; margin-bottom:15px;"><?php echo $message; ?></p>
    <?php endif; ?>
    <form action="db/logindb.php" method="POST">
      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" required>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit" class="btn-register">Login</button>
    </form>
    <span class="login-link">Don't have an account? <a href="register.php">Register here</a></span>
  </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>