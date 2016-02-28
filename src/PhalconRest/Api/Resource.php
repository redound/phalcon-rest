<?php

namespace PhalconRest\Api;

use Phalcon\Acl;
use Phalcon\Di;
use PhalconRest\Constants\ErrorCodes;
use PhalconRest\Constants\HttpMethods;
use PhalconRest\Constants\PostedDataMethods;
use PhalconRest\Constants\Services;
use PhalconRest\Exception;
use PhalconRest\Transformers\ModelTransformer;
use PhalconRest\Mvc\Controllers\CrudResourceController;

class Resource extends \Phalcon\Mvc\Micro\Collection implements \PhalconRest\Acl\MountableInterface
{
    protected $name;
    protected $description;

    protected $model;
    protected $transformer;

    protected $singleKey = 'item';
    protected $multipleKey = 'items';

    protected $allowedRoles = [];
    protected $deniedRoles = [];

    protected $postedDataMethod = PostedDataMethods::AUTO;

    protected $endpoints = [];

    protected $_modelPrimaryKey;

    public function __construct($prefix)
    {
        parent::setPrefix($prefix);
    }

    /**
     * @return string Unique identifier for this resource (returns the prefix)
     */
    public function getIdentifier()
    {
        return $this->getPrefix();
    }

    /**
     * @param string $name Name for the resource
     *
     * @return static
     */
    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null Name of the resource
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $description Description of the resource
     *
     * @return static
     */
    public function description($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string Description of the resource
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function setPrefix($prefix)
    {
        throw new Exception(ErrorCodes::GENERAL_SYSTEM, 'Setting prefix after initialization is prohibited.');
    }

    public function handler($handler, $lazy = true)
    {
        $this->setHandler($handler, $lazy);
        return $this;
    }

    /**
     * @param string $model Classname of the model
     *
     * @return static
     */
    public function model($model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return string|null Classname of the model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return string|null Primary key of the model
     */
    public function getModelPrimaryKey()
    {
        if (!$this->model) {
            return null;
        }

        if (!$this->_modelPrimaryKey) {

            /** @var \Phalcon\Mvc\Model\MetaData $modelsMetaData */
            $modelsMetaData = Di::getDefault()->get(Services::MODELS_METADATA);

            $modelClass = $this->model;

            $this->_modelPrimaryKey = $modelsMetaData->getIdentityField(new $modelClass);
        }

        return $this->_modelPrimaryKey;
    }

    /**
     * @param string $transformer Classname of the transformer
     *
     * @return static
     */
    public function transformer($transformer)
    {
        $this->transformer = $transformer;
        return $this;
    }

    /**
     * @return string|null Classname of the transformer
     */
    public function getTransformer()
    {
        return $this->transformer;
    }

    /**
     * Mounts endpoint to the resource
     *
     * @param \PhalconRest\Api\Endpoint $endpoint Endpoint to mount
     *
     * @return static
     */
    public function endpoint(Endpoint $endpoint)
    {
        $this->endpoints[] = $endpoint;

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

    /**
     * Mounts endpoint to the resource
     *
     * @param \PhalconRest\Api\Endpoint $endpoint Endpoint to mount (shortcut for endpoint function)
     *
     * @return static
     */
    public function mount(Endpoint $endpoint)
    {
        $this->endpoint($endpoint);
        return $this;
    }

    /**
     * @return \PhalconRest\Api\Endpoint[] Array of all mounted endpoints
     */
    public function getEndpoints()
    {
        return $this->endpoints;
    }

    /**
     * @param string $name Name for the endpoint to return
     *
     * @return \PhalconRest\Api\Endpoint|null Endpoint with the given name
     */
    public function getEndpoint($name)
    {
        return array_key_exists($name, $this->endpoints) ? $this->endpoints[$name] : null;
    }

    /**
     * @param string $singleKey Response key for single item
     *
     * @return static
     */
    public function singleKey($singleKey)
    {
        $this->singleKey = $singleKey;
        return $this;
    }

    /**
     * @return string Response key for single item
     */
    public function getSingleKey()
    {
        return $this->singleKey;
    }

    /**
     * @param string $multipleKey Response key for multiple items
     *
     * @return static
     */
    public function multipleKey($multipleKey)
    {
        $this->multipleKey = $multipleKey;
        return $this;
    }

    /**
     * @return string Response key for multiple items
     */
    public function getMultipleKey()
    {
        return $this->multipleKey;
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
     * Allows access to this resource for role with the given names. This can be overwritten on the Endpoint level.
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

    /***
     * Denies access to this resource for role with the given names. This can be overwritten on the Endpoint level.
     *
     * @param array ...$roleNames Names of the roles to deny
     *
     * @return $this
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

    public function getAclResources()
    {
        $apiEndpointIdentifiers = array_map(function (Endpoint $apiEndpoint) {
            return $apiEndpoint->getIdentifier();
        }, $this->endpoints);

        return [
            [new \Phalcon\Acl\Resource($this->getIdentifier(), $this->getName()), $apiEndpointIdentifiers]
        ];
    }

    public function getAclRules(array $roles)
    {
        $allowedResponse = [];
        $deniedResponse = [];

        $defaultAllowedRoles = $this->allowedRoles;
        $defaultDeniedRoles = $this->deniedRoles;

        foreach ($roles as $role) {

            /** @var Endpoint $apiEndpoint */
            foreach ($this->endpoints as $apiEndpoint) {

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

    protected function createRouteName(Endpoint $endpoint)
    {
        return serialize([
            'resource' => $this->getIdentifier(),
            'endpoint' => $endpoint->getIdentifier()
        ]);
    }

    /**
     * Returns resource with default values
     *
     * @param string $prefix Prefix for the resource (e.g. /user)
     * @param string $name Name for the resource (e.g. users) (optional)
     *
     * @return static
     */
    public static function factory($prefix, $name = null)
    {
        $resource = new Resource($prefix);

        $resource
            ->singleKey('item')
            ->multipleKey('items')
            ->transformer(ModelTransformer::class)
            ->setHandler(CrudResourceController::class, true);

        if ($name) {
            $resource->name($name);
        }

        return $resource;
    }

    /**
     * Returns resource with default values & all, find, create, update and delete endpoints pre-configured
     *
     * @param string $prefix Prefix for the resource (e.g. /user)
     * @param string $name Name for the resource (e.g. users) (optional)
     *
     * @return static
     */
    public static function crud($prefix, $name = null)
    {
        return self::factory($prefix, $name)
            ->endpoint(Endpoint::all())
            ->endpoint(Endpoint::find())
            ->endpoint(Endpoint::create())
            ->endpoint(Endpoint::update())
            ->endpoint(Endpoint::remove());
    }
}