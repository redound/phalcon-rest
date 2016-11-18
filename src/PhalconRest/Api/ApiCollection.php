<?php

namespace PhalconRest\Api;

use Phalcon\Acl;
use Phalcon\Mvc\Micro\CollectionInterface;
use PhalconApi\Acl\MountableInterface;
use PhalconApi\Constants\ErrorCodes;
use PhalconApi\Constants\HttpMethods;
use PhalconApi\Constants\PostedDataMethods;
use PhalconApi\Core;
use PhalconApi\Exception;

class ApiCollection extends \Phalcon\Mvc\Micro\Collection implements MountableInterface, CollectionInterface
{
    protected $name;
    protected $description;

    protected $allowedRoles = [];
    protected $deniedRoles = [];

    protected $postedDataMethod = PostedDataMethods::AUTO;

    protected $endpointsByName = [];


    public function __construct($prefix)
    {
        parent::setPrefix($prefix);

        $this->initialize();
    }

    /**
     * Use this method when you extend this class in order to define the collection
     */
    protected function initialize()
    {
    }

    /**
     * Returns collection with default values
     *
     * @param string $prefix Prefix for the collection (e.g. /auth)
     * @param string $name Name for the collection (e.g. authentication) (optional)
     *
     * @return static
     */
    public static function factory($prefix, $name = null)
    {
        $calledClass = get_called_class();

        /** @var \PhalconRest\Api\ApiCollection $collection */
        $collection = new $calledClass($prefix);

        if ($name) {
            $collection->name($name);
        }

        return $collection;
    }

    /**
     * @param string $name Name for the collection
     *
     * @return static
     */
    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $description Description of the collection
     *
     * @return static
     */
    public function description($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string Description of the collection
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function setPrefix($prefix)
    {
        throw new Exception(ErrorCodes::GENERAL_SYSTEM, null, 'Setting prefix after initialization is prohibited.');
    }

    public function handler($handler, $lazy = true)
    {
        $this->setHandler($handler, $lazy);
        return $this;
    }

    /**
     * Mounts endpoint to the collection
     *
     * @param \PhalconRest\Api\ApiEndpoint $endpoint Endpoint to mount (shortcut for endpoint function)
     *
     * @return static
     */
    public function mount(ApiEndpoint $endpoint)
    {
        $this->endpoint($endpoint);
        return $this;
    }

    /**
     * Mounts endpoint to the collection
     *
     * @param \PhalconRest\Api\ApiEndpoint $endpoint Endpoint to mount
     *
     * @return static
     */
    public function endpoint(ApiEndpoint $endpoint)
    {
        $this->endpointsByName[$endpoint->getName()] = $endpoint;

        switch ($endpoint->getHttpMethod()) {

            case HttpMethods::GET:

                $this->get($endpoint->getPath(), $endpoint->getHandlerMethod(), $this->createRouteName($endpoint));
                break;

            case HttpMethods::POST:

                $this->post($endpoint->getPath(), $endpoint->getHandlerMethod(), $this->createRouteName($endpoint));
                break;

            case HttpMethods::PUT:

                $this->put($endpoint->getPath(), $endpoint->getHandlerMethod(), $this->createRouteName($endpoint));
                break;

            case HttpMethods::DELETE:

                $this->delete($endpoint->getPath(), $endpoint->getHandlerMethod(), $this->createRouteName($endpoint));
                break;
        }

        return $this;
    }

    protected function createRouteName(ApiEndpoint $endpoint)
    {
        return serialize([
            'collection' => $this->getIdentifier(),
            'endpoint' => $endpoint->getIdentifier()
        ]);
    }

    /**
     * @return string Unique identifier for this collection (returns the prefix)
     */
    public function getIdentifier()
    {
        return $this->getPrefix();
    }

    /**
     * @return \PhalconRest\Api\ApiEndpoint[] Array of all mounted endpoints
     */
    public function getEndpoints()
    {
        return array_values($this->endpointsByName);
    }

    /**
     * @param string $name Name for the endpoint to return
     *
     * @return \PhalconRest\Api\ApiEndpoint|null Endpoint with the given name
     */
    public function getEndpoint($name)
    {
        return array_key_exists($name, $this->endpointsByName) ? $this->endpointsByName[$name] : null;
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
     * Allows access to this collection for role with the given names. This can be overwritten on the Endpoint level.
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

    /***
     * Denies access to this collection for role with the given names. This can be overwritten on the Endpoint level.
     *
     * @param ...array $roleNames Names of the roles to deny
     *
     * @return $this
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

    public function getAclResources()
    {
        $apiEndpointIdentifiers = array_map(function (ApiEndpoint $apiEndpoint) {
            return $apiEndpoint->getIdentifier();
        }, $this->endpointsByName);

        return [
            [new \Phalcon\Acl\Resource($this->getIdentifier(), $this->getName()), $apiEndpointIdentifiers]
        ];
    }

    /**
     * @return string|null Name of the collection
     */
    public function getName()
    {
        return $this->name;
    }

    public function getAclRules(array $roles)
    {
        $allowedResponse = [];
        $deniedResponse = [];

        $defaultAllowedRoles = $this->allowedRoles;
        $defaultDeniedRoles = $this->deniedRoles;

        foreach ($roles as $role) {

            /** @var ApiEndpoint $apiEndpoint */
            foreach ($this->endpointsByName as $apiEndpoint) {

                $rule = null;

                if (in_array($role, $defaultAllowedRoles)) {
                    $rule = true;
                }

                if (in_array($role, $defaultDeniedRoles)) {
                    $rule = false;
                }

                if (in_array($role, $apiEndpoint->getAllowedRoles())) {
                    $rule = true;
                }

                if (in_array($role, $apiEndpoint->getDeniedRoles())) {
                    $rule = false;
                }

                if ($rule === true) {
                    $allowedResponse[] = [$role, $this->getIdentifier(), $apiEndpoint->getIdentifier()];
                }

                if ($rule === false) {
                    $deniedResponse[] = [$role, $this->getIdentifier(), $apiEndpoint->getIdentifier()];
                }
            }
        }

        return [
            Acl::ALLOW => $allowedResponse,
            Acl::DENY => $deniedResponse
        ];
    }
}
