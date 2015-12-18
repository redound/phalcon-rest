<?php

namespace PhalconRest\Api;

class Endpoint
{
    protected $httpMethod;
    protected $path;
    protected $handlerMethod;

    public function __construct($path, $httpMethod, $handlerMethod)
    {
        $this->path = $path;
        $this->httpMethod = $httpMethod;
        $this->handlerMethod = $handlerMethod;
    }

    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    public function setHttpMethod($httpMethod)
    {
        $this->httpMethod = $httpMethod;
        return $this;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    public function getHandlerMethod()
    {
        return $this->handlerMethod;
    }

    public function setHandlerMethod($handlerMethod)
    {
        $this->handlerMethod = $handlerMethod;
        return $this;
    }
}