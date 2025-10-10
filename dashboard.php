<?php
session_start();
require_once 'lib.php';
$user = get_user();

// Check if user is logged in
if (!$user) {
  $_SESSION['error'] = 'Please log in to access the dashboard';
  header('Location: index.php');
  exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - OAuth Test Application</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 20px;
      background: #f5f5f5;
      color: #333;
    }

    .header {
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .header h1 {
      margin: 0;
      color: #333;
    }

    .nav-buttons a {
      background: #007bff;
      color: white;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: 5px;
      margin-left: 10px;
      transition: background-color 0.3s;
    }

    .nav-buttons a:hover {
      background: #0056b3;
    }

    .nav-buttons a.logout {
      background: #dc3545;
    }

    .nav-buttons a.logout:hover {
      background: #c82333;
    }

    .dashboard-content {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .card {
      background: white;
      padding: 25px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .card h2 {
      margin-top: 0;
      color: #333;
      border-bottom: 2px solid #007bff;
      padding-bottom: 10px;
    }

    .user-profile {
      text-align: center;
    }

    .avatar {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      margin-bottom: 15px;
    }

    .user-details {
      text-align: left;
    }

    .user-details dt {
      font-weight: bold;
      color: #555;
      margin-bottom: 5px;
    }

    .user-details dd {
      margin-bottom: 15px;
      color: #777;
    }

    .token-info {
      word-break: break-all;
      font-family: monospace;
      font-size: 12px;
      background: #f8f9fa;
      padding: 10px;
      border-radius: 4px;
      margin-top: 10px;
    }

    .api-test {
      background: #e8f5e8;
      border-left: 4px solid #28a745;
      padding: 15px;
      margin-top: 20px;
    }

    .provider-badge {
      background: #007bff;
      color: white;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: bold;
      text-transform: uppercase;
    }

    @media (max-width: 768px) {
      .dashboard-content {
        grid-template-columns: 1fr;
      }

      .header {
        flex-direction: column;
        text-align: center;
      }

      .nav-buttons {
        margin-top: 15px;
      }
    }
  </style>
</head>

<body>
  <div class="header">
    <h1>CentralAuth Dashboard</h1>
    <div class="nav-buttons">
      <a href="index.php">Home</a>
      <a href="logout.php" class="logout">Logout</a>
    </div>
  </div>

  <div class="dashboard-content">
    <div class="card user-profile">
      <h2>User Profile</h2>

      <?php if (!empty($user['gravatar'])): ?>
        <img src="<?= $user['gravatar'] ?>" alt="User Avatar" class="avatar">
      <?php endif; ?>

      <dl class="user-details">
        <dt>User ID:</dt>
        <dd><?= htmlspecialchars($user['id'] ?? 'N/A') ?></dd>

        <dt>Email:</dt>
        <dd><?= htmlspecialchars($user['email'] ?? 'N/A') ?></dd>

      </dl>
    </div>

    <div class="card">
      <h2>Session Information</h2>

      <dl class="user-details">
        <dt>Login Time:</dt>
        <dd><?= date('Y-m-d H:i:s') ?></dd>

        <?php if (isset($_SESSION['token_expires'])): ?>
          <dt>Token Expires:</dt>
          <dd><?= $_SESSION['token_expires'] ? date('Y-m-d H:i:s', $_SESSION['token_expires']) : 'No expiration' ?></dd>
        <?php endif; ?>

        <dt>Session ID:</dt>
        <dd><?= session_id() ?></dd>
      </dl>

      <div class="api-test">
        <h4>🔒 Protected Area</h4>
        <p>This dashboard is only accessible to authenticated users. The CentralAuth OAuth flow has successfully:</p>
        <ul>
          <li>✅ Redirected to CentralAuth provider</li>
          <li>✅ Handled authorization callback</li>
          <li>✅ Exchanged code for access token</li>
          <li>✅ Fetched user profile data</li>
          <li>✅ Created authenticated session</li>
        </ul>
      </div>

      <?php if (isset($_SESSION['access_token'])): ?>
        <div class="token-info">
          <strong>Access Token (first 50 characters):</strong><br>
          <?= htmlspecialchars(substr($_SESSION['access_token'], 0, 50)) ?>...
          <br><br>
          <small><em>Full token is stored in session for API calls</em></small>
        </div>
      <?php endif; ?>
    </div>

    <div class="card" style="grid-column: 1 / -1;">
      <h2>OAuth Flow Test Results</h2>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 20px;">
        <div style="text-align: center; padding: 15px; background: #d4edda; border-radius: 8px;">
          <h4 style="margin: 0; color: #155724;">✅ Authorization</h4>
          <p style="margin: 5px 0; font-size: 12px;">Successfully redirected to OAuth provider</p>
        </div>

        <div style="text-align: center; padding: 15px; background: #d4edda; border-radius: 8px;">
          <h4 style="margin: 0; color: #155724;">✅ Token Exchange</h4>
          <p style="margin: 5px 0; font-size: 12px;">Authorization code exchanged for access token</p>
        </div>

        <div style="text-align: center; padding: 15px; background: #d4edda; border-radius: 8px;">
          <h4 style="margin: 0; color: #155724;">✅ User Data</h4>
          <p style="margin: 5px 0; font-size: 12px;">Successfully fetched user profile information</p>
        </div>

        <div style="text-align: center; padding: 15px; background: #d4edda; border-radius: 8px;">
          <h4 style="margin: 0; color: #155724;">✅ Session</h4>
          <p style="margin: 5px 0; font-size: 12px;">User session created and maintained</p>
        </div>
      </div>
    </div>
  </div>
</body>

</html>