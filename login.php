<?php
session_start();
require_once 'vendor/autoload.php';

use CentralAuth\OAuth2\Client\Provider\CentralAuth; // custom provider

// Load configuration
$config = require 'config.php';

try {
  // Capture desired return URL (page where user started login)
  $rawReturnUrl = $_GET['return_to'] ?? $_GET['r'] ?? null;
  if (!$rawReturnUrl && !empty($_SERVER['HTTP_REFERER'])) {
    // Use referer only if same host
    $referer = $_SERVER['HTTP_REFERER'];
    $refParts = parse_url($referer);
    $host = $_SERVER['HTTP_HOST'] ?? '';
    if (!empty($refParts['host']) && strcasecmp($refParts['host'], $host) === 0) {
      $rawReturnUrl = ($refParts['path'] ?? '/');
      if (!empty($refParts['query'])) {
        $rawReturnUrl .= '?' . $refParts['query'];
      }
    }
  }

  // Sanitize return URL (allow only same-site relative paths) and convert to absolute
  $returnUrl = null;
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
  $origin = $scheme . '://' . $host;
  if ($rawReturnUrl) {
    $decoded = rawurldecode($rawReturnUrl);
    // Strip any scheme/host
    $parts = parse_url($decoded);
    $isValid = true;
    if (!$parts) {
      $isValid = false;
    } else {
      if (isset($parts['scheme']) || isset($parts['host'])) {
        // Disallow absolute URLs to other domains
        $isValid = false;
      }
      $path = $parts['path'] ?? '/';
      if (strpos($path, '//') === 0) { // protocol-relative attempt
        $isValid = false;
      }
    }
    if ($isValid) {
      $path = $parts['path'] ?? '/';
      // Basic allowlist: ensure path starts with '/'
      if ($path[0] !== '/') {
        $path = '/' . $path;
      }
      $reconstructed = $path;
      if (!empty($parts['query'])) {
        $reconstructed .= '?' . $parts['query'];
      }
      $returnUrl = $origin . $reconstructed;
    }
  }

  // Build redirect URI, appending return_to if present
  $baseRedirect = $config['redirect_uri'];
  $redirectUri = $baseRedirect;
  if ($returnUrl) {
    $delimiter = (strpos($baseRedirect, '?') === false) ? '?' : '&';
    $redirectUri .= $delimiter . 'return_to=' . rawurlencode($returnUrl);
  }

  // Initialize CentralAuth OAuth provider (dynamic redirectUri if return_to added)
  $provider = new CentralAuth([
    'clientId' => $config['client_id'],
    'clientSecret' => $config['client_secret'],
    'redirectUri' => $redirectUri,
    'authorization_url' => $config['authorization_url'],
    'token_url' => $config['token_url'],
    'resource_owner_details_url' => $config['resource_owner_details_url'],
    'domain' => $config['authorization_url']
  ]);

  // Generate a random state parameter for CSRF protection.
  // Embed an HMAC of return_to (optional) to prevent tampering if passed back; for simplicity we store in session.
  $state = bin2hex(random_bytes(16));
  $_SESSION['oauth_state'] = $state;
  if ($returnUrl) {
    $_SESSION['post_login_return_to'] = $returnUrl; // absolute URL
  }

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
