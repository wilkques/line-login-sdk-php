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
    use Wilkques\LINE\LINE;

    $line = new LINE('<CHANNEL_ID>');
    // or
    $line = LINE::clientId('<CHANNEL_ID>');

    $code = $_GET['code'] ?? null;

    if ($code) {
        $token = $line->clientSecret('<CHANNEL_SECRET>')->token($code, '<REDIRECT_URI>');

        $userProfile = $line->userProfile($token->accessToken());

        exit;
    }

    $url = $line->generateLoginUrl(
        // Callback URL: https://developers.line.biz/console/channel/<channel id>/line-login
        'https://yourdomain.com',
        // Permissions requested from the user: https://developers.line.biz/en/docs/line-login/integrate-line-login/#scopes
        [
            'profile', 'openid', 'email'
        ]
    );

    // or

    $url = $line->generateLoginUrl([
        // Callback URL: https://developers.line.biz/console/channel/<channel id>/line-login
        'redirect_uri'  => $url,
        // Permissions requested from the user: https://developers.line.biz/en/docs/line-login/integrate-line-login/#scopes
        'scope'         => ['openid', 'openid']
    ]);

    // or

    $url = $line->generateLoginUrl([
        // Callback URL: https://developers.line.biz/console/channel/<channel id>/line-login
        $url,
        // Permissions requested from the user: https://developers.line.biz/en/docs/line-login/integrate-line-login/#scopes
        ['openid', 'openid']
    ]);
    ````

1. PKCE Authorization
    1. command `composer require wilkques/pkce-php`
    1.  ```php
        use Wilkques\LINE\LINE;
        use Wilkques\PKCE\Generator;

        $line = new LINE('<CHANNEL_ID>');
        // or
        $line = LINE::clientId('<CHANNEL_ID>');

        $pkce = Generator::generate();

        $code = $_GET['code'] ?? null;

        if ($code) {
            $codeVerifier = $_GET['state'] ?? null;
        
            $token = $line->clientSecret('<CHANNEL_SECRET>')->token($code, '<REDIRECT_URI>', $codeVerifier);

            $userProfile = $line->userProfile($token->accessToken());

            exit;
        }

        $codeVerifier = $pkce->getCodeVerifier();

        $codeChallenge = $pkce->getCodeChallenge();

        $url = $line->generatePKCELoginUrl(
            // Callback URL: https://developers.line.biz/console/channel/<channel id>/line-login
            'https://yourdomain.com',
            // Permissions requested from the user: https://developers.line.biz/en/docs/line-login/integrate-line-login/#scopes
            [
                'profile', 'openid', 'email'
            ], 
            $codeVerifier, 
            $codeChallenge
        );

        // or

        $url = $line->generatePKCELoginUrl([
            // Callback URL: https://developers.line.biz/console/channel/<channel id>/line-login
            'redirect_uri'      => $url,
            // Permissions requested from the user: https://developers.line.biz/en/docs/line-login/integrate-line-login/#scopes
            'scope'             => ['openid', 'openid'], 
            'state'             => $codeVerifier, 
            'code_challenge'    => $codeChallenge,
        ]);

        // or

        $url = $line->generatePKCELoginUrl([
            // Callback URL: https://developers.line.biz/console/channel/<channel id>/line-login
            $url,
            // Permissions requested from the user: https://developers.line.biz/en/docs/line-login/integrate-line-login/#scopes
            ['openid', 'openid'], 
            $codeVerifier, 
            $codeChallenge,
        ]);
        ```

## REFERENCE

1. [Official](https://developers.line.biz/en/reference/line-login/)
1. [PKCE support for LINE Login](https://developers.line.biz/en/docs/line-login/integrate-pkce/)
