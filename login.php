<?php
session_start();
$config = require 'config.php';
require_once 'lib.php';
$provider = getProvider();

try {
  $returnUrl = $_SERVER['HTTP_REFERER'] ?? null;
  // Build redirect URI, appending return_to if present
  $baseRedirect = $config['redirect_uri'];
  $redirectUri = $baseRedirect;
  if ($returnUrl) {
    $delimiter = (strpos($baseRedirect, '?') === false) ? '?' : '&';
    $redirectUri .= $delimiter . 'return_to=' . rawurlencode($returnUrl);
  }

  // Generate a random state parameter for CSRF protection.
  // Embed an HMAC of return_to (optional) to prevent tampering if passed back; for simplicity we store in session.
  $state = bin2hex(random_bytes(16));
  $_SESSION['oauth_state'] = $state;

  // Get additional authorization parameters if needed
  $authParams = [
    'state' => $state
  ];

  // Create a high-entropy code verifier (43-128 characters per RFC 7636)
  $randomBytes = random_bytes(64);
  $codeVerifier = rtrim(strtr(base64_encode($randomBytes), '+/', '-_'), '=');
  // Ensure length within 43-128
  $codeVerifier = substr($codeVerifier, 0, 128);
  if (strlen($codeVerifier) < 43) {
    // Pad if too short (unlikely with 64 random bytes)
    $codeVerifier = str_pad($codeVerifier, 43, 'A');
  }
  $_SESSION['pkce_code_verifier'] = $codeVerifier;

  $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');
  $authParams['code_challenge_method'] = 'S256';
  $authParams['code_challenge'] = $codeChallenge;

  // Optional: Add custom translations (example)
  // $translations = [
  //   'loginPageIntro' => 'Welcome to my website!',
  // ];
  // $authParams['translations'] = base64_encode(json_encode($translations));

  // Get the authorization URL (with PKCE params if enabled)
  $authorizationUrl = $provider->getAuthorizationUrl($authParams);

  // Redirect to CentralAuth OAuth provider
  header('Location: ' . $authorizationUrl);
  exit;
} catch (Exception $e) {
  $_SESSION['error'] = 'OAuth initialization failed: ' . $e->getMessage();
  header('Location: index.php');
  exit;
}
