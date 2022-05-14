<?php

namespace Wilkques\LINE;

use Wilkques\Http\Client;
use Wilkques\Http\Response;
use Wilkques\LINE\DataObjects\IdToken;
use Wilkques\LINE\DataObjects\Token;
use Wilkques\LINE\DataObjects\UserProfile;
use Wilkques\LINE\Exceptions\FriendshipStatusException;
use Wilkques\LINE\Exceptions\RefreshTokenException;
use Wilkques\LINE\Exceptions\RevokeTokenException;
use Wilkques\LINE\Exceptions\TokenException;
use Wilkques\LINE\Exceptions\UserProfileException;
use Wilkques\LINE\Exceptions\VerifyIdTokenException;
use Wilkques\LINE\Exceptions\VerifyTokenException;
use Wilkques\LINE\Enum\UrlEnum;

/**
 * @method static static clientId() set client id
 * @method static static clientSecret() set client secret
 * @method \Wilkques\Http\Client asForm()
 * @method \Wilkques\Http\Client withToken(string $token, string $type = 'Bearer')
 * @method \Wilkques\Http\Client post(string $url, array $data, array $query = null)
 * @method \Wilkques\Http\Response throw(callable $callable = null)
 */
class LINE
{
    /** @var string */
    protected $clientId;
    /** @var string */
    protected $clientSecret;
    /** @var array */
    protected $methods = [
        'clientId', 'clientSecret'
    ];
    /** @var Client */
    protected $client;

    /**
     * @param string|null $clientId
     * @param string|null $clientSecret
     */
    public function __construct(string $clientId = null, string $clientSecret = null)
    {
        $this->setClientId($clientId)->setClientSecret($clientSecret);
    }

    /**
     * @param string|null $clientId
     * 
     * @return static
     */
    public function setClientId(string $clientId = null)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param string|null $clientSecret
     * 
     * @return static
     */
    public function setClientSecret(string $clientSecret = null)
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * @throws \UnexpectedValueException
     * 
     * @return static
     */
    private function checkClientId()
    {
        if (!$this->getClientId()) {
            throw new \UnexpectedValueException('ClientId is required');
        }

        return $this;
    }

    /**
     * @throws \UnexpectedValueException
     * 
     * @return static
     */
    private function checkClientSecret()
    {
        if (!$this->getClientSecret()) {
            throw new \UnexpectedValueException('ClientSecret is required');
        }

        return $this;
    }

    /**
     * @param string|array|null $redirectUri
     * @param array $scope
     * @param string|null $state
     * @param array $args
     * 
     * @return string
     * 
     * @see [Link a LINE Official Account with your channel](https://developers.line.biz/en/docs/line-login/link-a-bot/#displaying-the-option-to-add-your-line-official-account-as-a-friend)
     */
    public function generateLoginUrl(
        $redirectUri = null,
        array $scope = ['openid', 'profile'],
        string $state = null,
        array $args = []
    ) {
        is_array($redirectUri) && [
            'redirect_uri'  => $redirectUri,
            'scope'         => $scope,
            'state'         => $state,
        ] = $redirectUri;

        $params = [
            'response_type' => 'code',
            'client_id' => $this->checkClientId()->getClientId(),
            'redirect_uri' => $redirectUri ?: $this->getCurrentUrl(),
            'scope' => implode(' ', $scope ?: ['openid', 'profile']),
            'state' => $state ?: 'default',
        ];

        return UrlEnum::AUTH_URL . '?' . http_build_query(array_merge($params, $args), '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * @param string|array|null $redirectUri
     * @param array $scope
     * @param string|null $state
     * @param string|null $codeChallenge
     * @param array $args
     * 
     * @return string
     * 
     * @see [PKCE support for LINE Login](https://developers.line.biz/en/docs/line-login/integrate-pkce/#how-to-integrate-pkce)
     */
    public function generatePKCELoginUrl(
        $redirectUri = null,
        array $scope = ['openid', 'profile'],
        string $state = null,
        string $codeChallenge = null,
        array $args = []
    ) {
        is_array($redirectUri) && [
            'redirect_uri'  => $redirectUri,
            'scope'         => $scope,
            'state'         => $state,
        ] = $redirectUri;

        $params = [
            'code_challenge_method' => 'S256',
            'code_challenge' => $codeChallenge
        ];

        return $this->generateLoginUrl($redirectUri, $scope, $state, array_merge($params, $args));
    }

    /**
     * @param string $code
     * @param string $redirectUri
     * @param string|null $codeVerifier
     * 
     * @throws TokenException
     * 
     * @return Token
     * 
     * @see [Issue access token](https://developers.line.biz/en/reference/line-login/#issue-access-token)
     */
    public function token(string $code, string $redirectUri = null, string $codeVerifier = null)
    {
        $params = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->checkClientId()->getClientId(),
            'client_secret' => $this->checkClientSecret()->getClientSecret(),
            'redirect_uri' => $redirectUri ? $redirectUri : $this->getCurrentUrl(),
            'code' => $code,
        ];

        $codeVerifier && $params['code_verifier'] = $codeVerifier;

        return new Token(
            $this->asForm()->post(
                UrlEnum::TOKEN_URL,
                $params
            )->throw(function (Response $response) {
                return new TokenException($response);
            })
        );
    }

    /**
     * @param string $accessToken
     * 
     * @throws VerifyTokenException
     * 
     * @return Response
     * 
     * @see [Verify access token validity](https://developers.line.biz/en/reference/line-login/#verify-access-token)
     */
    public function verifyToken(string $accessToken)
    {
        return $this->withHeaders([
            'access_token' => $accessToken
        ])->get(UrlEnum::VERIFYTOKEN_URL)->throw(function (Response $response) {
            return new VerifyTokenException($response);
        });
    }

    /**
     * @param string $refreshToken
     * @param bool $webApp
     * 
     * @throws RefreshTokenException
     * 
     * @return Token
     * 
     * @see [Refresh access token](https://developers.line.biz/en/reference/line-login/#refresh-access-token)
     */
    public function refreshToken(string $refreshToken, bool $webApp = true)
    {
        $params = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $this->checkClientId()->getClientId(),
        ];

        $webApp && $params['client_secret'] = $this->checkClientSecret()->getClientSecret();

        return new Token(
            $this->asForm()->post(
                UrlEnum::TOKEN_URL,
                $params
            )->throw(function (Response $response) {
                return new RefreshTokenException($response);
            })
        );
    }

    /**
     * @param string $accessToken
     * @param bool $webApp
     * 
     * @throws RevokeTokenException
     * 
     * @return Response
     * 
     * @see [Revoke access token](https://developers.line.biz/en/reference/line-login/#revoke-access-token)
     */
    public function revokeToken(string $accessToken, bool $webApp = true)
    {
        $params = [
            'access_token' => $accessToken,
            'client_id' => $this->checkClientId()->getClientId(),
        ];

        $webApp && $params['client_secret'] = $this->checkClientSecret()->getClientSecret();

        return $this->asForm()->post(
            UrlEnum::REVOKE_URL,
            $params
        )->throw(function (Response $response) {
            return new RevokeTokenException($response);
        });
    }

    /**
     * @param string $idToken
     * @param array|[] $options
     * 
     * @throws VerifyIdTokenException
     * 
     * @return IdToken
     * 
     * @see [Verify ID token](https://developers.line.biz/en/reference/line-login/#verify-id-token)
     */
    public function verifyIdToken(string $idToken, array $options = [])
    {
        $params = [
            'id_token' => $idToken,
            'client_id' => $this->checkClientId()->getClientId(),
        ];

        return new IdToken(
            $this->asForm()->post(
                UrlEnum::VERIFYTOKEN_URL,
                $params + $options
            )->throw(function (Response $response) {
                return new VerifyIdTokenException($response);
            })
        );
    }

    /**
     * @param string $accessToken
     * 
     * @throws UserProfileException
     * 
     * @return UserProfile
     * 
     * @see [Get user profile](https://developers.line.biz/en/reference/line-login/#profile)
     */
    public function userProfile(string $accessToken)
    {
        return new UserProfile(
            $this->withToken($accessToken)->get(UrlEnum::PROFILE_URL)->throw(function (Response $response) {
                return new UserProfileException($response);
            })
        );
    }

    /**
     * @param string $accessToken
     * 
     * @throws FriendshipStatusException
     * 
     * @return bool
     * 
     * @see [Friendship status](https://developers.line.biz/en/reference/line-login/#get-friendship-status)
     */
    public function friendshipStatus(string $accessToken)
    {
        return $this->withToken($accessToken)->get(UrlEnum::VERIFYTOKEN_URL)->throw(function (Response $response) {
            return new FriendshipStatusException($response);
        })->json()['friendFlag'];
    }

    /**
     * @return string
     */
    private function getCurrentUrl()
    {
        $http = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $uri = explode('?', $_SERVER['REQUEST_URI'], 2)[0];

        return "$http://{$_SERVER['HTTP_HOST']}$uri";
    }

    /**
     * @param Client $client
     * 
     * @return static
     */
    public function setClient(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return Client
     */
    public function newClient()
    {
        return $this->getClient() ?? $this->setClient(new Client);
    }

    /**
     * @param string $method
     * @param array $arguments
     * 
     * @return static|Client
     */
    public function __call(string $method, array $arguments)
    {
        $method = ltrim(trim($method));

        if (in_array($method, $this->methods)) {
            $method = 'set' . ucfirst($method);

            return $this->{$method}(...$arguments);
        }

        return $this->newClient()->{$method}(...$arguments);
    }

    /**
     * @param string $method
     * @param array $arguments
     * 
     * @return static
     */
    public static function __callStatic(string $method, array $arguments)
    {
        return (new static)->{$method}(...$arguments);
    }
}
