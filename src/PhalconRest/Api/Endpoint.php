<?php

namespace PhalconRest\Api;

class Endpoint
{
    protected $name;

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

    /**
     * @param string $name  Name for the endpoint
     *
     * @return static
     */
    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $httpMethod  HTTP method for the endpoint
     *
     * @return static
     */
    public function httpMethod($httpMethod)
    {
        $this->httpMethod = $httpMethod;
        return $this;
    }

    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * @param string $path  Path for the endpoint
     *
     * @return static
     */
    public function path($path)
    {
        $this->path = $path;
        return $this;
    }

    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $handlerMethod  Method in the controller to be called
     *
     * @return static
     */
    public function handlerMethod($handlerMethod)
    {
        $this->handlerMethod = $handlerMethod;
        return $this;
    }

    public function getHandlerMethod()
    {
        return $this->handlerMethod;
    }

    /**
     * @param string $name
     * @param string $path
     * @param string $httpMethod
     * @param string $handlerMethod
     *
     * @return static
     */
    public static function create($name=null, $path=null, $httpMethod=null, $handlerMethod=null)
    {
        $endpoint = new Endpoint($path, $httpMethod, $handlerMethod);

        if($name){
            $endpoint->name($name);
        }

        return $endpoint;
    }
}