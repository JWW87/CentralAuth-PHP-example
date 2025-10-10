<?php
require_once 'vendor/autoload.php';

use CentralAuth\OAuth2\Client\Provider\CentralAuth; // custom provider
use League\OAuth2\Client\Token\AccessToken;

// Function to create and return the CentralAuth provider instance
function getProvider()
{
  // Load configuration
  $config = require 'config.php';
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
function getUser()
{
  if (!empty($_SESSION['access_token'])) {
    $provider = getProvider();
    $accessToken = new AccessToken(['access_token' => $_SESSION['access_token']]);
    //Get the user info if we have an access token
    $resourceOwner = $provider->getResourceOwner($accessToken);
    return $resourceOwner->toArray();
  }
}
