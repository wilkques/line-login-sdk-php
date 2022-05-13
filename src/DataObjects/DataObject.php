<?php

namespace Wilkques\LINE\DataObjects;

use Wilkques\Http\Response;

abstract class DataObject
{
    /** @var array */
    protected $data = [];
    /** @var Response */
    protected $response;

    /**
     * @param Response|array $response
     */
    public function __construct($response)
    {
        is_array($response) && $this->setData($response);

        $response instanceof Response && $this->setResponse($response);
    }

    /**
     * @param Response $response
     * 
     * @return static
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param array|null $data
     * 
     * @return static
     */
    public function setData(array $data = null)
    {
        $this->data = $data ?: $this->getResponse()->json();

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data ?: $this->setData();
    }

    /**
     * @param string $key
     * 
     * @return string
     */
    public function getDataByKey(string $key)
    {
        return $this->getData()[$key] ?? null;
    }
}
