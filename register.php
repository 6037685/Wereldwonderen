<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Adventure</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: url('https://images.unsplash.com/photo-1501785888041-af3ef285b470') no-repeat center center/cover;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .register-container {
      background: rgba(0, 0, 0, 0.6);
      padding: 40px;
      border-radius: 15px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.4);
      text-align: center;
      color: white;
    }

    .register-container h2 {
      margin-bottom: 25px;
      font-size: 28px;
      color: #ff9800;
    }

    .form-group {
      margin-bottom: 20px;
      text-align: left;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-size: 14px;
    }

    .form-group input {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 8px;
      outline: none;
      font-size: 16px;
    }

    .btn-register {
      width: 100%;
      padding: 14px;
      border: none;
      border-radius: 30px;
      background: #ff9800;
      color: black;
      font-weight: bold;
      font-size: 18px;
      cursor: pointer;
      transition: transform 0.3s, background 0.3s;
    }

    .btn-register:hover {
      transform: scale(1.05);
      background: #ffc107;
    }

    .login-link {
      margin-top: 15px;
      font-size: 14px;
      display: block;
    }

    .login-link a {
      color: #ff9800;
      text-decoration: none;
      font-weight: bold;
    }

    .login-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="register-container">
    <h2>🌍 Register</h2>
    <form>
      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" placeholder="Enter your name" required>
      </div>
      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" placeholder="Enter your email" required>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" placeholder="Enter your password" required>
      </div>
      <div class="form-group">
        <label for="confirm">Confirm Password</label>
        <input type="password" id="confirm" placeholder="Confirm your password" required>
      </div>
      <button type="submit" class="btn-register">Register</button>
    </form>
    <span class="login-link">Already have an account? <a href="login.html">Login here</a></span>
  </div>
</body>
</html>
