<?php

namespace Wilkques\Line;

use Wilkques\Http\Response as HttpClientResponse;
use Wilkques\LINE\LINE;

/**
 * @method static int status()
 * @method static string body()
 * @method static array json()
 * @method static array headers()
 * @method static string|null header()
 * @method static boolean ok()
 * @method static boolean redirect()
 * @method static boolean successful()
 * @method static boolean failed()
 * @method static boolean clientError()
 * @method static boolean serverError()
 * @method static throws throw(callable $callback = null)
 */
class Response
{
    /** @var LINE */
    protected $line;
    /** @var HttpClientResponse */
    protected $response;

    /**
     * @param LINE $line
     * @param HttpClientResponse $response
     */
    public function __construct(LINE $line, HttpClientResponse $response)
    {
        $this->setLINE($line)->setResponse($response);
    }

    /**
     * @param LINE $line
     * 
     * @return static
     */
    public function setLINE(LINE $line)
    {
        $this->line = $line;

        return $this;
    }

    /**
     * @return LINE
     */
    public function getLINE()
    {
        return $this->line;
    }

    /**
     * @param HttpClientResponse $response
     * 
     * @return static
     */
    public function setResponse(HttpClientResponse $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return HttpClientResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return mixed
     */
    public function getResponseByKey(string $key)
    {
        return $this->json()[$key];
    }

    /**
     * @param string $method
     * @param array $arguments
     * 
     * @return HttpClientResponse
     */
    public function __call(string $method, array $arguments)
    {
        return $this->getResponse()->{$method}(...$arguments);
    }
}
