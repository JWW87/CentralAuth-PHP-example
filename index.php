<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OAuth Test Application</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .container {
      background: white;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      text-align: center;
      max-width: 400px;
      width: 90%;
    }

    h1 {
      color: #333;
      margin-bottom: 10px;
    }

    .subtitle {
      color: #666;
      margin-bottom: 30px;
      font-size: 16px;
    }

    .login-btn {
      background: #4285f4;
      color: white;
      border: none;
      padding: 12px 30px;
      font-size: 16px;
      border-radius: 5px;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
      transition: background-color 0.3s;
      margin: 10px 0;
    }

    .login-btn:hover {
      background: #3367d6;
    }

    .github-btn {
      background: #333;
    }

    .github-btn:hover {
      background: #24292e;
    }

    .status {
      margin-top: 20px;
      padding: 15px;
      border-radius: 5px;
      font-weight: bold;
    }

    .success {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .error {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    .user-info {
      background: #e2e3e5;
      padding: 20px;
      border-radius: 5px;
      margin-top: 20px;
      text-align: left;
    }

    .logout-btn {
      background: #dc3545;
      color: white;
      border: none;
      padding: 8px 20px;
      font-size: 14px;
      border-radius: 3px;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
      margin-top: 15px;
    }

    .logout-btn:hover {
      background: #c82333;
    }
  </style>
</head>

<body>
  <div class="container">
    <h1>CentralAuth OAuth Test Application</h1>
    <p class="subtitle">Test your CentralAuth OAuth integration</p>

    <?php
    // Load configuration to get provider name
    $config = require 'config.php';

    // Check if user is logged in
    if (isset($_SESSION['user'])) {
      echo '<div class="status success">Successfully logged in!</div>';
      echo '<div class="user-info">';
      echo '<h3>User Information:</h3>';
      echo '<strong>Email:</strong> ' . htmlspecialchars($_SESSION['user']['email'] ?? 'N/A') . '<br>';
      echo '<strong>ID:</strong> ' . htmlspecialchars($_SESSION['user']['id'] ?? 'N/A') . '<br>';
      if (isset($_SESSION['user']['gravatar'])) {
        echo '<strong>Avatar:</strong><br><img src="' . htmlspecialchars($_SESSION['user']['gravatar']) . '" alt="Avatar" style="width: 60px; height: 60px; border-radius: 50%; margin-top: 10px;"><br>';
      }
      echo '</div>';
      echo '<a href="logout.php" class="logout-btn">Logout</a>';
      echo '<br><br>';
      echo '<a href="dashboard.php" class="login-btn">Go to Dashboard</a>';
    } else {
      // Show login options
      echo '<a href="login.php" class="login-btn">Login with CentralAuth</a>';

      // Show any error messages
      if (isset($_SESSION['error'])) {
        echo '<div class="status error">' . htmlspecialchars($_SESSION['error']) . '</div>';
        unset($_SESSION['error']);
      }

      // Show any success messages
      if (isset($_SESSION['success'])) {
        echo '<div class="status success">' . htmlspecialchars($_SESSION['success']) . '</div>';
        unset($_SESSION['success']);
      }
    }
    ?>

    <div style="margin-top: 30px; font-size: 14px; color: #666;">
      <p><strong>Setup Instructions:</strong></p>
      <ol style="text-align: left; font-size: 12px;">
        <li>Update <code>.env</code> with your CentralAuth endpoints and credentials</li>
        <li>Configure your app's redirect URI to: <code>http://localhost/callback.php</code></li>
        <li>Make sure your web server is running on localhost</li>
      </ol>
    </div>
  </div>
</body>

</html>