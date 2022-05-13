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
    use Wilkques\Line\Login;

    $line = new LINE('<CHANNEL_ID>', '<CHANNEL_SECRET>');
    // or
    $line = LINE::clientId('<CHANNEL_ID>')->clientSecret('<CHANNEL_SECRET>');

    $url = $line->generateLoginUrl();

    $token = $line->token('<CODE>', '<REDIRECT_URI>');
    ````

1. PKCE Authorization
    1. command `composer require wilkques/pkce-php`
    1.  ```php
        use Wilkques\Line\LINE;
        use Wilkques\PKCE\Generator;

        $line = new LINE('<CHANNEL_ID>', '<CHANNEL_SECRET>');
        // or
        $line = LINE::clientId('<CHANNEL_ID>')->clientSecret('<CHANNEL_SECRET>');

        $pkce = Generator::generate();

        $codeVerifier = $pkce->getCodeVerifier();

        $codeChallenge = $pkce->getCodeChallenge();

        $url = $line->generatePKCELoginUrl($codeChallenge);

        $token = $line->token('<CODE>', '<REDIRECT_URI>', $codeVerifier);
        ```

## REFERENCE

1. [Official](https://developers.line.biz/en/reference/line-login/)
1. [PKCE support for LINE Login](https://developers.line.biz/en/docs/line-login/integrate-pkce/)
