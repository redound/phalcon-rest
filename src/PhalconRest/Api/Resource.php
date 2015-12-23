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
     * @param string $name Name for the resource
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

    public function getModel()
    {
        return $this->model;
    }

    public function getModelPrimaryKey()
    {
        if (!$this->_modelPrimaryKey) {

            /** @var \Phalcon\Mvc\Model\MetaData $modelsMetaData */
            $modelsMetaData = Di::getDefault()->get(Services::MODELS_METADATA);

            $modelClass = $this->getModel();

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

    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param string $name Name for the endpoint
     * @param Endpoint $endpoint
     *
     * @return static
     */
    public function endpoint(Endpoint $endpoint)
    {
        $this->prefixLock = true;

        $this->endpoints[] = $endpoint;

        switch ($endpoint->getHttpMethod()) {

            case HttpMethods::GET:

                $this->get($endpoint->getPath(), $endpoint->getHandlerMethod(), $this->_createRouteName($endpoint));
                break;

            case HttpMethods::POST:

                $this->post($endpoint->getPath(), $endpoint->getHandlerMethod(), $this->_createRouteName($endpoint));
                break;

            case HttpMethods::PUT:

                $this->put($endpoint->getPath(), $endpoint->getHandlerMethod(), $this->_createRouteName($endpoint));
                break;

            case HttpMethods::DELETE:

                $this->delete($endpoint->getPath(), $endpoint->getHandlerMethod(), $this->_createRouteName($endpoint));
                break;
        }

        return $this;
    }

    protected function _createRouteName(Endpoint $endpoint)
    {
        return serialize([
            'resourcePrefix' => $this->getPrefix() ? $this->getPrefix() : '',
            'endpointPath' => $endpoint->getPath(),
            'httpMethod' => $endpoint->getHttpMethod()
        ]);
    }

    /**
     * @param Endpoint $endpoint Endpoint to mound (shortcut for endpoint function)
     *
     * @return static
     * @throws Exception
     */
    public function mount(Endpoint $endpoint)
    {
        $this->endpoint($endpoint);
        return $this;
    }

    public function getEndpoints()
    {
        return $this->endpoints;
    }

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

    public function getMultipleKey()
    {
        return $this->multipleKey;
    }

    public function allow($roleName)
    {
        if (!in_array($roleName, $this->allowedRoles)) {
            $this->allowedRoles[] = $roleName;
        }

        return $this;
    }

    public function getAllowedRoles()
    {
        return $this->allowedRoles;
    }

    public function deny($roleName)
    {
        if (!in_array($roleName, $this->deniedRoles)) {
            $this->deniedRoles[] = $roleName;
        }

        return $this;
    }

    public function getDeniedRoles()
    {
        return $this->deniedRoles;
    }

    public static function factory($prefix)
    {
        $resource = new Resource($prefix);

        $resource
            ->singleKey('item')
            ->multipleKey('items')
            ->transformer(ModelTransformer::class)
            ->controller(CrudResourceController::class);

        return $resource;
    }

    public static function crud($prefix)
    {
        $resource = Resource::factory($prefix)
            ->endpoint(Endpoint::all())
            ->endpoint(Endpoint::find())
            ->endpoint(Endpoint::create())
            ->endpoint(Endpoint::update())
            ->endpoint(Endpoint::delete());

        return $resource;
    }
}