<?php

namespace PhalconRest\Api;

class Endpoint
{
    protected $httpMethod;
    protected $path;
    protected $handlerMethod;

    public function __construct($path=null, $httpMethod=null, $handlerMethod=null)
    {
        $this->path($path);
        $this->httpMethod($httpMethod);
        $this->handlerMethod($handlerMethod);

        return $this;
    }

    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    public function httpMethod($httpMethod)
    {
        $this->httpMethod = $httpMethod;
        return $this;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function path($path)
    {
        $this->path = $path;
        return $this;
    }

    public function getHandlerMethod()
    {
        return $this->handlerMethod;
    }

    public function handlerMethod($handlerMethod)
    {
        $this->handlerMethod = $handlerMethod;
        return $this;
    }
}