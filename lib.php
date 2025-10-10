<?php
require_once 'vendor/autoload.php';
require_once 'config.php';

use CentralAuth\OAuth2\Client\Provider\CentralAuth; // custom provider
use League\OAuth2\Client\Token\AccessToken;

// Function to create and return the CentralAuth provider instance
function get_provider()
{
  // Load configuration
  $config = get_config();
  return new CentralAuth([
    'clientId' => $config['client_id'],
    'clientSecret' => $config['client_secret'],
    'redirectUri' => $config['redirect_uri'],
    'authorization_url' => $config['authorization_url'],
    'token_url' => $config['token_url'],
    'resource_owner_details_url' => $config['resource_owner_details_url'],
    'domain' => $config['redirect_uri']
  ]);
}

// Function to get the authenticated user's data from CentralAuth
function get_user()
{
  if (!empty($_SESSION['access_token'])) {
    $provider = get_provider();
    $accessToken = new AccessToken(['access_token' => $_SESSION['access_token']]);
    //Get the user info if we have an access token
    $resourceOwner = $provider->getResourceOwner($accessToken);
    return $resourceOwner->toArray();
  }
}
