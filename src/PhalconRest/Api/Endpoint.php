<?php

namespace PhalconRest\Api;

use PhalconRest\Constants\HttpMethods;

class Endpoint
{
    protected $name;

    protected $httpMethod;
    protected $path;
    protected $handlerMethod;

    public function __construct($path, $httpMethod = HttpMethods::GET, $handlerMethod = null)
    {
        $this->path = $path;
        $this->httpMethod = $httpMethod;
        $this->handlerMethod($handlerMethod);

        return $this;
    }

    /**
     * @param string $handlerMethod Method in the controller to be called
     *
     * @return static
     */
    public function handlerMethod($handlerMethod)
    {
        $this->handlerMethod = $handlerMethod;
        return $this;
    }

    /**
     * @param string $path
     * @param string $httpMethod
     * @param string $handlerMethod
     *
     * @return static
     */
    public static function factory($path, $httpMethod = HttpMethods::GET, $handlerMethod = null)
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

    /**
     * @param string $name Name for the endpoint
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

    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getHandlerMethod()
    {
        return $this->handlerMethod;
    }
}