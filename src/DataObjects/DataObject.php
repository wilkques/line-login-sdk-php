<?php

namespace Wilkques\LINE\DataObjects;

use Wilkques\Helpers\Arrays;
use Wilkques\Http\Response;
use Wilkques\LINE\LINE;

abstract class DataObject implements \JsonSerializable, \ArrayAccess
{
    /** @var array */
    protected $data = [];

    /** @var Response */
    protected $response;

    /** @var LINE */
    protected $line;

    /**
     * @param Response|array $response
     */
    public function __construct($response, ?LINE $line = null)
    {
        is_array($response) && $this->setData($response);

        $response instanceof Response && $this->setResponse($response)->setData();

        $line && $this->setLine($line);
    }


    /**
     * @param LINE $line
     * 
     * @return static
     */
    public function setLine(LINE $line)
    {
        $this->line = $line;

        return $this;
    }

    /**
     * @return LINE
     */
    public function getLine()
    {
        return $this->line;
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
    public function setData(?array $data = null)
    {
        $this->data = $data ?: $this->toArray();

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data ?: $this->setData()->getData();
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

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->json();
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->__get($offset);
    }

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->__set($offset, $value);
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return !array_key_exists($offset, $this->toArray()) && !is_null($this->__get($offset));
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->toArray()[$offset]);
    }

    /**
     * Determine if an attribute or relation exists on the model.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Unset an attribute on the model.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }

    /**
     * @return array
     */
    public function __serialize(): array
    {
        return $this->toArray();
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function __set(string $key, $value)
    {
        Arrays::set($this->data, $key, $value);
    }

    /**
     * @param string $key
     * 
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->getDataByKey($key);
    }

    /**
     * @param string $method
     * @param array $arguments
     * 
     * @return static|Response
     */
    public function __call(string $method, array $arguments)
    {
        return $this->getResponse()->{$method}(...$arguments);
    }
}
