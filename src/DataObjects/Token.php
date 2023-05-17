<?php

namespace Wilkques\LINE\DataObjects;

class Token extends DataObject
{
    /**
     * @return string|null
     */
    public function accessToken()
    {
        return $this->getDataByKey('access_token');
    }

    /**
     * @return string|null
     */
    public function expiredIn()
    {
        return $this->getDataByKey('expires_in');
    }

    /**
     * @return string|null
     */
    public function refreshToken()
    {
        return $this->getDataByKey('refresh_token');
    }

    /**
     * @return string|null
     */
    public function scope()
    {
        return $this->getDataByKey('scope');
    }

    /**
     * @return string|null
     */
    public function tokenType()
    {
        return $this->getDataByKey('token_type');
    }

    /**
     * @return string|null
     */
    public function idToken()
    {
        return $this->getDataByKey('id_token');
    }
}
