<?php

namespace PhalconRest\Api;

use Phalcon\Di;
use PhalconRest\Constants\Http;
use PhalconRest\Constants\Services;

class Resource extends \Phalcon\Mvc\Micro\Collection
{
    protected $model;
    protected $transformer;
    protected $controller;

    protected $singleKey = 'item';
    protected $multipleKey = 'items';

    protected $endpoints;

    protected $_modelPrimaryKey;


    public function __construct($prefix=null, $model=null, $singleKey='item', $multipleKey='items', $transformer='\PhalconRest\Transformer\Model', $controller='\PhalconRest\Mvc\Controller\Resource')
    {
        $this->setPrefix($prefix);

        $this->setModel($model);
        $this->setSingleKey($singleKey);
        $this->setMultipleKey($multipleKey);

        $this->setTransformer($transformer);
        $this->setController($controller);

        $this->endpoints = [];
    }


    public function setModel($model)
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
            $modelsMetaData = Di::getDefault()->get(Services::MODELS_METADATA);

            $modelClass = $this->getModel();

            $this->_modelPrimaryKey = $modelsMetaData->getIdentityField(new $modelClass);
        }

        return $this->_modelPrimaryKey;
    }


    public function setTransformer($transformer)
    {
        $this->transformer = $transformer;
        return $this;
    }

    public function getTransformer()
    {
        return $this->transformer;
    }


    public function getController()
    {
        return $this->controller;
    }

    public function setController($controller)
    {
        $this->controller = $controller;

        $controller = new $controller();

        if($controller instanceof \PhalconRest\Mvc\Controller\Resource){
            $controller->setResource($this);
        }

        $this->setHandler($controller);

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

    public function setEndpoint($name, Endpoint $endpoint)
    {
        $this->endpoints[$name] = $endpoint;

        switch($endpoint->getHttpMethod()){

            case Http::GET:

                $this->get($endpoint->getPath(), $endpoint->getHandlerMethod());
                break;

            case Http::POST:

                $this->post($endpoint->getPath(), $endpoint->getHandlerMethod());
                break;

            case Http::PUT:

                $this->put($endpoint->getPath(), $endpoint->getHandlerMethod());
                break;

            case Http::DELETE:

                $this->delete($endpoint->getPath(), $endpoint->getHandlerMethod());
                break;
        }

        return $this;
    }


    public function getSingleKey()
    {
        return $this->singleKey;
    }

    public function setSingleKey($singleKey)
    {
        $this->singleKey = $singleKey;
        return $this;
    }


    public function getMultipleKey()
    {
        return $this->multipleKey;
    }

    public function setMultipleKey($multipleKey)
    {
        $this->multipleKey = $multipleKey;
        return $this;
    }
}