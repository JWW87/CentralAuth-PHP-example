<?php
session_start();
require_once 'vendor/autoload.php';

use CentralAuth\OAuth2\Client\Provider\CentralAuth; // custom provider

// Load configuration
$config = require 'config.php';

// Check if we have the required parameters
if (!isset($_GET['code']) || !isset($_GET['state'])) {
  $_SESSION['error'] = 'OAuth callback missing required parameters';
  header('Location: index.php');
  exit;
}

// Verify the state parameter to prevent CSRF attacks
if (!isset($_SESSION['oauth_state']) || $_GET['state'] !== $_SESSION['oauth_state']) {
  $_SESSION['error'] = 'Invalid OAuth state parameter';
  header('Location: index.php');
  exit;
}

// Get the provider that was used for login
$provider_name = "CentralAuth";

try {
  // Recreate the CentralAuth provider configuration
  $provider = new CentralAuth([
    'clientId' => $config['client_id'],
    'clientSecret' => $config['client_secret'],
    'redirectUri' => $config['redirect_uri'],
    'authorization_url' => $config['authorization_url'],
    'token_url' => $config['token_url'],
    'resource_owner_details_url' => $config['resource_owner_details_url'],
    'domain' => $config['authorization_url']
  ]);

  // Build token request parameters
  $tokenParams = [
    'code' => $_GET['code']
  ];
  // Include PKCE code_verifier if we used PKCE
  if (!empty($_SESSION['pkce_code_verifier'])) {
    $tokenParams['code_verifier'] = $_SESSION['pkce_code_verifier'];
  }
  // Exchange the authorization code for an access token (with PKCE if applicable)
  $accessToken = $provider->getAccessToken('authorization_code', $tokenParams);

  // Use provider resource owner (internally handles POST pattern)
  $resourceOwner = $provider->getResourceOwner($accessToken);
  $userData = $resourceOwner->toArray();
  if (!isset($userData['email']))
    throw new Exception('Invalid user info response: missing email');

  // Store user data in session
  $_SESSION['user'] = $userData;
  $_SESSION['access_token'] = $accessToken->getToken();
  $_SESSION['token_expires'] = $accessToken->getExpires();

  // Clean up OAuth session variables
  unset($_SESSION['oauth_state']);
  unset($_SESSION['oauth_provider']);
  unset($_SESSION['pkce_code_verifier']); // Remove PKCE secret from session

  $_SESSION['success'] = 'Successfully logged in with CentralAuth!';

  // Get post-login return URL
  $returnUrl = $_GET['return_to'] ?? null;

  if (!$returnUrl) {
    $returnUrl = $origin . '/index.php';
  }

  header('Location: ' . $returnUrl);
  exit;
} catch (Exception $e) {
  $_SESSION['error'] = 'OAuth callback failed: ' . $e->getMessage();
  header('Location: index.php');
  exit;
}
