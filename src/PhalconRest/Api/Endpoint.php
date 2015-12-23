<?php

namespace PhalconRest\Api;

use PhalconRest\Constants\HttpMethods;

class Endpoint
{
    protected $name;

    protected $httpMethod;
    protected $path;
    protected $handlerMethod;

    public function __construct($path = null, $httpMethod = null, $handlerMethod = null)
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
     * @param string $path
     * @param string $httpMethod
     * @param string $handlerMethod
     *
     * @return static
     */
    public static function factory($path = null, $httpMethod = null, $handlerMethod = null)
    {
        return new Endpoint($path, $httpMethod, $handlerMethod);
    }

    public static function all()
    {
        return new Endpoint('/', HttpMethods::GET, 'all');
    }

    public static function create()
    {
        return new Endpoint('/', HttpMethods::POST, 'create');
    }

    public static function update()
    {
        return new Endpoint('/{id}', HttpMethods::PUT, 'update');
    }

    public static function delete()
    {
        return new Endpoint('/{id}', HttpMethods::DELETE, 'delete');
    }

    public static function find()
    {
        return new Endpoint('/{id}', HttpMethods::GET, 'find');
    }
}