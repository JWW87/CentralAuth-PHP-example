<?php
// Loads environment-based configuration for the OAuth provider.
// Secrets and environment-specific values are read from .env.

use Dotenv\Dotenv;

// Ensure composer autoload is available if this file is standalone included.
if (!class_exists(Dotenv::class)) {
  require_once __DIR__ . '/vendor/autoload.php';
}

// Load .env only once (skip if already loaded in another entry point)
static $envLoaded = false;
if (!$envLoaded && file_exists(__DIR__ . '/.env')) {
  $dotenv = Dotenv::createImmutable(__DIR__);
  $dotenv->load();
  $envLoaded = true;
}

// Helper to safely fetch env variables with optional default.
function env_or($key, $default = null)
{
  return isset($_ENV[$key]) ? $_ENV[$key] : (isset($_SERVER[$key]) ? $_SERVER[$key] : $default);
}

return [
  'client_id' => env_or('OAUTH_CLIENT_ID', ''),
  'client_secret' => env_or('OAUTH_CLIENT_SECRET', ''),
  'redirect_uri' => env_or('OAUTH_REDIRECT_URI', 'http://localhost/callback.php'),
  'authorization_url' => env_or('OAUTH_AUTHORIZATION_URL', ''),
  'token_url' => env_or('OAUTH_TOKEN_URL', ''),
  'resource_owner_details_url' => env_or('OAUTH_RESOURCE_OWNER_DETAILS_URL', ''),
];
