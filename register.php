<?php
session_start();
$error = '';
if (isset($_SESSION['register_error'])) {
    $error = $_SESSION['register_error'];
    unset($_SESSION['register_error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Adventure</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <?php include 'header.php'; ?>

  <main>
    <div class="register-container">
      <h2>🌍 Register</h2>
      <?php if ($error): ?>
        <p style="color:#ff6b6b; font-weight:bold; margin-bottom:15px;"><?php echo $error; ?></p>
      <?php endif; ?>
      <form action="db/registerdb.php" method="POST">
        <div class="form-group">
          <label for="name">Full Name</label>
          <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
          <label for="confirm">Confirm Password</label>
          <input type="password" id="confirm" name="confirm" required>
        </div>
        <div class="form-group">
          <label for="role">Choose your role</label>
          <select id="role" name="role" required>
            <option value="">-- Select Role --</option>
            <option value="onderzoeker">Onderzoeker</option>
            <option value="redacteur">Redacteur</option>
            <option value="bezoeker">Bezoeker</option>
            <option value="archivaris">Archivaris</option>
          </select>
        </div>
        <button type="submit" class="btn-register">Register</button>
      </form>
      <span class="login-link">Already have an account? <a href="login.php">Login here</a></span>
    </div>
  </main>

  <?php include 'footer.php'; ?>

</body>
</html>
