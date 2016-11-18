<?php

namespace PhalconRest\Api;

use PhalconApi\Constants\HttpMethods;
use PhalconApi\Constants\PostedDataMethods;
use PhalconApi\Core;

class ApiEndpoint
{
    const ALL = 'all';
    const FIND = 'find';
    const CREATE = 'create';
    const UPDATE = 'update';
    const REMOVE = 'remove';

    protected $name;
    protected $description;

    protected $httpMethod;
    protected $path;
    protected $handlerMethod;

    protected $postedDataMethod = PostedDataMethods::AUTO;
    protected $exampleResponse;

    protected $allowedRoles = [];
    protected $deniedRoles = [];


    public function __construct($path, $httpMethod = HttpMethods::GET, $handlerMethod = null)
    {
        $this->path = $path;
        $this->httpMethod = $httpMethod;
        $this->handlerMethod = $handlerMethod;
    }

    /**
     * Returns pre-configured all endpoint
     *
     * @return static
     */
    public static function all()
    {
        return self::factory('/', HttpMethods::GET, 'all')
            ->name(self::ALL)
            ->description('Returns all items');
    }

    /**
     * @param string $description Description for the endpoint
     *
     * @return static
     */
    public function description($description)
    {
        $this->description = $description;
        return $this;
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
        return new ApiEndpoint($path, $httpMethod, $handlerMethod);
    }

    /**
     * Returns pre-configured find endpoint
     *
     * @return static
     */
    public static function find()
    {
        return self::factory('/{id}', HttpMethods::GET, 'find')
            ->name(self::FIND)
            ->description('Returns the item identified by {id}');
    }

    /**
     * Returns pre-configured create endpoint
     *
     * @return static
     */
    public static function create()
    {
        return self::factory('/', HttpMethods::POST, 'create')
            ->name(self::CREATE)
            ->description('Creates a new item using the posted data');
    }

    /**
     * Returns pre-configured update endpoint
     *
     * @return static
     */
    public static function update()
    {
        return self::factory('/{id}', HttpMethods::PUT, 'update')
            ->name(self::UPDATE)
            ->description('Updates an existing item identified by {id}, using the posted data');
    }

    /**
     * Returns pre-configured remove endpoint
     *
     * @return static
     */
    public static function remove()
    {
        return self::factory('/{id}', HttpMethods::DELETE, 'remove')
            ->name(self::REMOVE)
            ->description('Removes the item identified by {id}');
    }

    /**
     * Returns pre-configured GET endpoint
     *
     * @param $path
     * @param string $handlerMethod
     *
     * @return ApiEndpoint
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
     * @return ApiEndpoint
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
     * @return ApiEndpoint
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
     * @return ApiEndpoint
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
     * @return ApiEndpoint
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
     * @return ApiEndpoint
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
     * @return ApiEndpoint
     */
    public static function patch($path, $handlerMethod = null)
    {
        return self::factory($path, HttpMethods::PATCH, $handlerMethod);
    }

    /**
     * @return string Unique identifier for this endpoint (returns a combination of the HTTP method and the path)
     */
    public function getIdentifier()
    {
        return $this->getHttpMethod() . ' ' . $this->getPath();
    }

    /**
     * @return string HTTP method of the endpoint
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * @return string Path of the endpoint, relative to the collection
     */
    public function getPath()
    {
        return $this->path;
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
     * @return string|null Name of the endpoint
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string Description for the endpoint
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $exampleResponse Example of the response of the endpoint
     *
     * @return $this
     */
    public function exampleResponse($exampleResponse)
    {
        $this->exampleResponse = $exampleResponse;
        return $this;
    }

    /**
     * @return string Example of the response of the endpoint
     */
    public function getExampleResponse()
    {
        return $this->exampleResponse;
    }

    /**
     * @return string $method One of the method constants defined in PostedDataMethods
     */
    public function getPostedDataMethod()
    {
        return $this->postedDataMethod;
    }

    /**
     * Sets the posted data method to POST
     *
     * @return static
     */
    public function expectsPostData()
    {
        $this->postedDataMethod(PostedDataMethods::POST);
        return $this;
    }

    /**
     * @param string $method One of the method constants defined in PostedDataMethods
     *
     * @return static
     */
    public function postedDataMethod($method)
    {
        $this->postedDataMethod = $method;
        return $this;
    }

    /**
     * Sets the posted data method to JSON_BODY
     *
     * @return static
     */
    public function expectsJsonData()
    {
        $this->postedDataMethod(PostedDataMethods::JSON_BODY);
        return $this;
    }

    /**
     * Allows access to this endpoint for role with the given names.
     *
     * @param ...array $roleNames Names of the roles to allow
     *
     * @return static
     */
    public function allow()
    {
        $roleNames = func_get_args();

        // Flatten array to allow array inputs
        $roleNames = Core::array_flatten($roleNames);

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
     * @param ...array $roleNames Names of the roles to allow
     *
     * @return static
     */
    public function deny()
    {
        $roleNames = func_get_args();

        // Flatten array to allow array inputs
        $roleNames = Core::array_flatten($roleNames);

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
}
