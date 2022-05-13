<?php

namespace Wilkques\LINE\DataObjects;

class UserProfile extends DataObject
{
    /**
     * @return string|null
     */
    public function userId()
    {
        return $this->getDataByKey('userId');
    }

    /**
     * @return string|null
     */
    public function displayName()
    {
        return $this->getDataByKey('displayName');
    }

    /**
     * @return string|null
     */
    public function pictureUrl()
    {
        return $this->getDataByKey('pictureUrl');
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
    public function statusMessage()
    {
        return $this->getDataByKey('statusMessage');
    }
}
