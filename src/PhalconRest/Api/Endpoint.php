<?php

namespace PhalconRest\Api;

use PhalconRest\Constants\HttpMethods;

class Endpoint
{
    const ALL = 'all';
    const FIND = 'find';
    const CREATE = 'create';
    const UPDATE = 'update';
    const REMOVE = 'remove';

    protected $name;

    protected $httpMethod;
    protected $path;
    protected $handlerMethod;

    protected $allowedRoles = [];
    protected $deniedRoles = [];

    public function __construct($path, $httpMethod = HttpMethods::GET, $handlerMethod = null)
    {
        $this->path = $path;
        $this->httpMethod = $httpMethod;
        $this->handlerMethod = $handlerMethod;
    }

    /**
     * @return string Unique identifier for this endpoint (returns a combination of the HTTP method and the path)
     */
    public function getIdentifier()
    {
        return $this->getHttpMethod() . ' ' . $this->getPath();
    }

    /**
     * @param string $handlerMethod Name of controller-method to be called for the endpoint
     *
     * @return static
     */
    public function handlerMethod($handlerMethod)
    {
        $this->handlerMethod = $handlerMethod;
        return $this;
    }

    /**
     * @return string Name of controller-method to be called for the endpoint
     */
    public function getHandlerMethod()
    {
        return $this->handlerMethod;
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

    /**
     * @return string|null Name of the endpoint
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string HTTP method of the endpoint
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * @return string Path of the endpoint, relative to the resource
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Allows access to this endpoint for role with the given names.
     *
     * @param array ...$roleNames Names of the roles to allow
     *
     * @return static
     */
    public function allow(...$roleNames)
    {
        foreach ($roleNames as $role) {

            if (!in_array($role, $this->allowedRoles)) {
                $this->allowedRoles[] = $role;
            }
        }

        return $this;
    }

    /**
     * @return string[] Array of allowed role-names
     */
    public function getAllowedRoles()
    {
        return $this->allowedRoles;
    }

    /**
     * Denies access to this endpoint for role with the given names.
     *
     * @param array ...$roleNames Names of the roles to allow
     *
     * @return static
     */
    public function deny(...$roleNames)
    {
        foreach ($roleNames as $role) {

            if (!in_array($role, $this->deniedRoles)) {
                $this->deniedRoles[] = $role;
            }
        }

        return $this;
    }

    /**
     * @return string[] Array of denied role-names
     */
    public function getDeniedRoles()
    {
        return $this->deniedRoles;
    }

    /**
     * Returns endpoint with default values
     *
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

    /**
     * Returns pre-configured all endpoint
     *
     * @return static
     */
    public static function all()
    {
        return self::factory('/', HttpMethods::GET, 'all')->name(self::ALL);
    }

    /**
     * Returns pre-configured find endpoint
     *
     * @return static
     */
    public static function find()
    {
        return self::factory('/{id}', HttpMethods::GET, 'find')->name(self::FIND);
    }

    /**
     * Returns pre-configured create endpoint
     *
     * @return static
     */
    public static function create()
    {
        return self::factory('/', HttpMethods::POST, 'create')->name(self::CREATE);
    }

    /**
     * Returns pre-configured update endpoint
     *
     * @return static
     */
    public static function update()
    {
        return self::factory('/{id}', HttpMethods::PUT, 'update')->name(self::UPDATE);
    }

    /**
     * Returns pre-configured remove endpoint
     *
     * @return static
     */
    public static function remove()
    {
        return self::factory('/{id}', HttpMethods::DELETE, 'remove')->name(self::REMOVE);
    }

    /**
     * Returns pre-configured GET endpoint
     *
     * @param $path
     * @param string $handlerMethod
     *
     * @return Endpoint
     */
    public static function get($path, $handlerMethod = null)
    {
        return self::factory($path, HttpMethods::GET, $handlerMethod);
    }

    /**
     * Returns pre-configured POST endpoint
     *
     * @param $path
     * @param string $handlerMethod
     *
     * @return Endpoint
     */
    public static function post($path, $handlerMethod = null)
    {
        return self::factory($path, HttpMethods::POST, $handlerMethod);
    }

    /**
     * Returns pre-configured PUT endpoint
     *
     * @param $path
     * @param string $handlerMethod
     *
     * @return Endpoint
     */
    public static function put($path, $handlerMethod = null)
    {
        return self::factory($path, HttpMethods::PUT, $handlerMethod);
    }

    /**
     * Returns pre-configured DELETE endpoint
     *
     * @param $path
     * @param string $handlerMethod
     *
     * @return Endpoint
     */
    public static function delete($path, $handlerMethod = null)
    {
        return self::factory($path, HttpMethods::DELETE, $handlerMethod);
    }

    /**
     * Returns pre-configured HEAD endpoint
     *
     * @param $path
     * @param string $handlerMethod
     *
     * @return Endpoint
     */
    public static function head($path, $handlerMethod = null)
    {
        return self::factory($path, HttpMethods::HEAD, $handlerMethod);
    }

    /**
     * Returns pre-configured OPTIONS endpoint
     *
     * @param $path
     * @param string $handlerMethod
     *
     * @return Endpoint
     */
    public static function options($path, $handlerMethod = null)
    {
        return self::factory($path, HttpMethods::OPTIONS, $handlerMethod);
    }

    /**
     * Returns pre-configured PATCH endpoint
     *
     * @param $path
     * @param string $handlerMethod
     *
     * @return Endpoint
     */
    public static function patch($path, $handlerMethod = null)
    {
        return self::factory($path, HttpMethods::PATCH, $handlerMethod);
    }
}