<?php

namespace PhalconRest\Api;

use Phalcon\Di;
use PhalconRest\Constant\ErrorCode;
use PhalconRest\Constant\Http;
use PhalconRest\Constant\Service;
use PhalconRest\Exception;

class Resource extends \Phalcon\Mvc\Micro\Collection
{
    protected $name;

    protected $model;
    protected $transformer;
    protected $controller;

    protected $singleKey = 'item';
    protected $multipleKey = 'items';

    protected $endpoints = [];

    protected $_modelPrimaryKey;


    public function __construct(
        $prefix = null,
        $model = null,
        $singleKey = 'item',
        $multipleKey = 'items',
        $transformer = '\PhalconRest\Transformer\Model',
        $controller = '\PhalconRest\Mvc\Controller\Resource'
    ) {
        $this->prefix($prefix);
        $this->model($model);
        $this->singleKey($singleKey);
        $this->multipleKey($multipleKey);
        $this->transformer($transformer);
        $this->controller($controller);

        return $this;
    }

    /**
     * @param string $name  Name for the resource
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
     * @param string $prefix  Route prefix
     *
     * @return static
     */
    public function prefix($prefix)
    {
        $this->setPrefix($prefix);
        return $this;
    }

    /**
     * @param string $model  Classname of the model
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
        if(!$this->_modelPrimaryKey){

            /** @var \Phalcon\Mvc\Model\MetaData $modelsMetaData */
            $modelsMetaData = Di::getDefault()->get(Service::MODELS_METADATA);

            $modelClass = $this->getModel();

            $this->_modelPrimaryKey = $modelsMetaData->getIdentityField(new $modelClass);
        }

        return $this->_modelPrimaryKey;
    }

    /**
     * @param string $transformer  Classname of the transformer
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
     * @param string $controller  Classname of the controller
     *
     * @return static
     */
    public function controller($controller)
    {
        $this->controller = $controller;

        if($controller){

            $controller = new $controller();

            if($controller instanceof \PhalconRest\Mvc\Controller\ResourceController){
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
     * @param string $name  Name for the endpoint
     * @param Endpoint $endpoint
     *
     * @return static
     */
    public function endpoint(Endpoint $endpoint)
    {
        $this->endpoints[] = $endpoint;

        switch($endpoint->getHttpMethod()){

            case Http::GET:

                $this->get($endpoint->getPath(), $endpoint->getHandlerMethod(), $this->getName() . '/');
                break;

            case Http::POST:

                $this->post($endpoint->getPath(), $endpoint->getHandlerMethod(), $this->getName() . '/');
                break;

            case Http::PUT:

                $this->put($endpoint->getPath(), $endpoint->getHandlerMethod(), $this->getName() . '/');
                break;

            case Http::DELETE:

                $this->delete($endpoint->getPath(), $endpoint->getHandlerMethod(), $this->getName() . '/');
                break;
        }

        return $this;
    }

    /**
     * @param Endpoint $endpoint  Endpoint to mound (shortcut for endpoint function)
     *
     * @return static
     * @throws Exception
     */
    public function mount(Endpoint $endpoint)
    {
        if(!$endpoint->getName()){
            throw new Exception(ErrorCode::GEN_SYSTEM, 'No name provided for endpoint');
        }

        $this->endpoint($endpoint->getName(), $endpoint);
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
     * @param string $singleKey  Response key for single item
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
     * @param string $multipleKey  Response key for multiple items
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

    public static function factory(
        $prefix = null,
        $model = null,
        $singleKey = 'item',
        $multipleKey = 'items',
        $transformer = '\PhalconRest\Transformer\ModelTransformer',
        $controller = '\PhalconRest\Mvc\Controller\ResourceController'
    ) {

        return new Resource(
            $prefix,
            $model,
            $singleKey,
            $multipleKey,
            $transformer,
            $controller
        );
    }

    public static function crud(
        $prefix = null,
        $model = null,
        $singleKey = 'item',
        $multipleKey = 'items',
        $transformer = '\PhalconRest\Transformer\ModelTransformer',
        $controller = '\PhalconRest\Mvc\Controller\CrudResourceController'
    ) {

        $resource = new Resource(
            $prefix,
            $model,
            $singleKey,
            $multipleKey,
            $transformer,
            $controller
        );

        $resource->endpoint(Endpoint::all())
            ->endpoint(Endpoint::find())
            ->endpoint(Endpoint::create())
            ->endpoint(Endpoint::update())
            ->endpoint(Endpoint::delete());

        return $resource;
    }
}