<?php

namespace Wilkques\LINE\Exceptions;

use Wilkques\Helpers\Arrays;
use Wilkques\Http\Response;

class Exception extends \Exception
{
    /** @var array */
    protected $errorResponse = [];
    /** @var Response */
    protected $response;

    /**
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->setResponse($response);

        parent::__construct($this->prepareMessage(), $response->status());
    }

    /**
     * Prepare the exception message.
     * 
     * @return string
     */
    protected function prepareMessage()
    {
        $response = $this->getResponse();

        $data = $response->json();

        $message = "HTTP request returned status code {$response->status()}";

        if (is_array($data) && array_intersect(array_flip($data), ['error', 'error_description'])) {
            $error = Arrays::get($data, 'error');

            $errorDescription = Arrays::get($data, 'error_description');

            $this->setError($error)->setErrorDescription($errorDescription);

            return $message . ":\n Error:{$this->getError()}\n Error Description: {$this->getErrorDescription()}\n";
        }

        $summary = $response->body();

        return is_null($summary) ? $message : $message .= ":\n{$summary}\n";
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
     * @param string $error
     * 
     * @return static
     */
    public function setError(?string $error = null)
    {
        Arrays::set($this->errorResponse, 'error', $error);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getError()
    {
        return Arrays::get($this->errorResponse, 'error');
    }

    /**
     * @param string $errorDescription
     * 
     * @return static
     */
    public function setErrorDescription(?string $errorDescription = null)
    {
        Arrays::set($this->errorResponse, 'errorDescription', $errorDescription);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getErrorDescription()
    {
        return Arrays::get($this->errorResponse, 'errorDescription');
    }
}
