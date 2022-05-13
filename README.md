# Line Login for PHP

[![Latest Stable Version](https://poser.pugx.org/wilkques/line-login-sdk-php/v/stable)](https://packagist.org/packages/wilkques/line-login-sdk-php)
[![License](https://poser.pugx.org/wilkques/line-login-sdk-php/license)](https://packagist.org/packages/wilkques/line-login-sdk-php)

## Installation
````
composer require wilkques/line-login-sdk-php
````
## How to use
1. Authorization
    ````php
    use Wilkques\Line\LINE;

    $line = new LINE('<CHANNEL_ID>');
    // or
    $line = LINE::clientId('<CHANNEL_ID>');

    $code = $_GET['code'] ?? null;

    if ($code) {
        $token = $line->clientSecret('<CHANNEL_SECRET>')->token($code, '<REDIRECT_URI>');

        $userProfile = $token->userProfile();

        exit;
    }

    $url = $line->generateLoginUrl([
        // Callback URL: https://developers.line.biz/console/channel/<channel id>/line-login
        'redirect_uri'  => 'https://yourdomain.com',
        // Permissions requested from the user: https://developers.line.biz/en/docs/line-login/integrate-line-login/#scopes
        'scope'         => 'profile openid email',
    ]);

    // or

    $url = $line->generateLoginUrl(
        // Callback URL: https://developers.line.biz/console/channel/<channel id>/line-login
        'https://yourdomain.com',
        // Permissions requested from the user: https://developers.line.biz/en/docs/line-login/integrate-line-login/#scopes
        [
            'profile', 'openid', 'email'
        ]
    );
    ````

1. PKCE Authorization
    1. command `composer require wilkques/pkce-php`
    1.  ```php
        use Wilkques\Line\LINE;
        use Wilkques\PKCE\Generator;

        $line = new LINE('<CHANNEL_ID>');
        // or
        $line = LINE::clientId('<CHANNEL_ID>');

        $pkce = Generator::generate();

        $codeVerifier = $pkce->getCodeVerifier();

        $codeChallenge = $pkce->getCodeChallenge();

        $code = $_GET['code'] ?? null;

        if ($code) {
            $token = $line->clientSecret('<CHANNEL_SECRET>')->token($code, '<REDIRECT_URI>', $codeVerifier);

            $userProfile = $token->userProfile();

            exit;
        }

        $url = $line->generatePKCELoginUrl([
            // Callback URL: https://developers.line.biz/console/channel/<channel id>/line-login
            'redirect_uri' => 'https://yourdomain.com',
            // Permissions requested from the user: https://developers.line.biz/en/docs/line-login/integrate-line-login/#scopes
            'scope' => [
                'profile', 'openid', 'email'
            ], 
            'state' => 'default', 
            'code_challenge' => $codeChallenge,
        ]);

        // or

        $url = $line->generatePKCELoginUrl(
            // Callback URL: https://developers.line.biz/console/channel/<channel id>/line-login
            'https://yourdomain.com',
            // Permissions requested from the user: https://developers.line.biz/en/docs/line-login/integrate-line-login/#scopes
            [
                'profile', 'openid', 'email'
            ], 
            'default', 
            $codeChallenge
        );
        ```

## REFERENCE

1. [Official](https://developers.line.biz/en/reference/line-login/)
1. [PKCE support for LINE Login](https://developers.line.biz/en/docs/line-login/integrate-pkce/)
