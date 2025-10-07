# CentralAuth OAuth Test Application

A minimal OAuth 2.0 Authorization Code flow test harness using a custom `CentralAuth` provider (extends `league/oauth2-client`).

For complete CentralAuth configuration and API documentation, visit: **[https://docs.centralauth.com](https://docs.centralauth.com)**

## Features
- Authorization Code + PKCE
- Custom CentralAuth provider with POST userinfo retrieval
- Session-based login + dashboard

## Setup
1. Install dependencies:
   - PHP 7.4+ (or compatible)
   - Composer
2. Install libraries:
```
composer install
```
3. Create your environment file:
```
copy .env.example .env   # Windows
# OR
cp .env.example .env      # macOS/Linux
```
4. Edit `.env` with your real credentials and endpoints.
5. Place this folder under your web root (e.g. XAMPP `htdocs`).
6. Visit: `http://localhost/index.php`

## Environment Variables (.env)
| Variable                         | Description                        |
| -------------------------------- | ---------------------------------- |
| OAUTH_CLIENT_ID                  | CentralAuth client ID              |
| OAUTH_CLIENT_SECRET              | CentralAuth client secret          |
| OAUTH_REDIRECT_URI               | Redirect URI (must match provider) |
| OAUTH_AUTHORIZATION_URL          | Authorization/ Login endpoint      |
| OAUTH_TOKEN_URL                  | Token / verification endpoint      |
| OAUTH_RESOURCE_OWNER_DETAILS_URL | User info endpoint                 |

## Custom Provider Usage Example
```php
use CentralAuth\OAuth2\Client\Provider\CentralAuth; // From centralauth/oauth2-centralauth package
$provider = new CentralAuth([
  'clientId' => $_ENV['OAUTH_CLIENT_ID'],
  'clientSecret' => $_ENV['OAUTH_CLIENT_SECRET'],
  'redirectUri' => $_ENV['OAUTH_REDIRECT_URI'],
  'authorization_url' => $_ENV['OAUTH_AUTHORIZATION_URL'],
  'token_url' => $_ENV['OAUTH_TOKEN_URL'],
  'resource_owner_details_url' => $_ENV['OAUTH_RESOURCE_OWNER_DETAILS_URL']
]);
```

## Security Notes
- Do not commit `.env` (ensure `.gitignore` contains it)
- Use production secrets through real environment configuration (Apache, Nginx, container, etc.)

## License
MIT
