<?php

namespace PhalconRest\Api;

use Phalcon\Di;
use PhalconRest\Constants\ErrorCodes;
use PhalconRest\Constants\HttpMethods;
use PhalconRest\Constants\Services;
use PhalconRest\Exception;
use PhalconRest\Transformers\ModelTransformer;
use PhalconRest\Mvc\Controllers\CrudResourceController;

class Resource extends \Phalcon\Mvc\Micro\Collection
{
    protected $name;

    protected $model;
    protected $transformer;
    protected $controller;

    protected $singleKey = 'item';
    protected $multipleKey = 'items';

    protected $allowedRoles = [];
    protected $deniedRoles = [];

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

    public function setPrefix($prefix)
    {
        throw new Exception(ErrorCodes::GENERAL_SYSTEM, 'Setting prefix after initialization is prohibited.');
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
     * @param string $controller Classname of the controller
     *
     * @return static
     */
    public function controller($controller)
    {
        $this->controller = $controller;

        if ($controller) {

            $controller = new $controller();

            if ($controller instanceof \PhalconRest\Mvc\ResourceInjectableInterface) {
                $controller->setResource($this);
            }

            $this->setHandler($controller);
        }

        return $this;
    }

    /**
     * @return string|null Classname of the controller
     */
    public function getController()
    {
        return $this->controller;
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
     * Allows access to this resource for role with the given names. This can be overwritten on the Endpoint level.
     *
     * @param array ...$roleNames Names of the roles to allow
     *
     * @return static
     */
    public function allow(...$roleNames)
    {
        foreach($roleNames as $role) {

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
        foreach($roleNames as $role) {

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
    public static function factory($prefix, $name=null)
    {
        $resource = new Resource($prefix);

        $resource
            ->singleKey('item')
            ->multipleKey('items')
            ->transformer(ModelTransformer::class)
            ->controller(CrudResourceController::class);

        if($name){
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
    public static function crud($prefix, $name=null)
    {
        return self::factory($prefix, $name)
            ->endpoint(Endpoint::all())
            ->endpoint(Endpoint::find())
            ->endpoint(Endpoint::create())
            ->endpoint(Endpoint::update())
            ->endpoint(Endpoint::remove());
    }
}