<?php

namespace Wilkques\LINE\DataObjects;

class IdToken extends DataObject
{
    /**
     * @return string|null
     */
    public function iss()
    {
        return $this->getDataByKey('iss');
    }

    /**
     * @return string|null
     */
    public function sub()
    {
        return $this->getDataByKey('sub');
    }

    /**
     * @return string|null
     */
    public function userId()
    {
        return $this->sub();
    }

    /**
     * @return string|null
     */
    public function aud()
    {
        return $this->getDataByKey('aud');
    }

    /**
     * @return string|null
     */
    public function exp()
    {
        return $this->getDataByKey('exp');
    }

    /**
     * @return string|null
     */
    public function iat()
    {
        return $this->getDataByKey('iat');
    }

    /**
     * @return string|null
     */
    public function nonce()
    {
        return $this->getDataByKey('nonce');
    }

    /**
     * @return string|null
     */
    public function amr()
    {
        return $this->getDataByKey('amr');
    }

    /**
     * @return string|null
     */
    public function name()
    {
        return $this->getDataByKey('name');
    }

    /**
     * @return string|null
     */
    public function displayName()
    {
        return $this->name();
    }

    /**
     * @return string|null
     */
    public function picture()
    {
        return $this->getDataByKey('picture');
    }

    /**
     * @return string|null
     */
    public function pictureUrl()
    {
        return $this->picture();
    }

    /**
     * @return string|null
     */
    public function email()
    {
        return $this->getDataByKey('email');
    }

    /**
     * @return string|null
     */
    public function authTime()
    {
        return $this->getDataByKey('auth_time');
    }

    /**
     * @return UserProfile
     */
    public function userProfile(string $token = null)
    {
        if ($token) {
            return $this->getLine()->userProfile($token);
        }

        $userProfile = new UserProfile([
            'userId' => $this->userId(),
            'displayName' => $this->displayName(),
            'pictureUrl' => $this->pictureUrl(),
            'email' => $this->email(),
        ], $this->getLine());

        $userProfile->setResponse($this->getResponse());

        return $userProfile;
    }
}
