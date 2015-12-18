<?php

namespace PhalconRest\Api;

use Phalcon\Di;
use PhalconRest\Constants\Http;
use PhalconRest\Constants\Services;

class Resource extends \Phalcon\Mvc\Micro\Collection
{
    protected $name;

    protected $model;
    protected $transformer;
    protected $controller;

    protected $singleKey = 'item';
    protected $multipleKey = 'items';

    protected $endpoints;

    protected $_modelPrimaryKey;


    public function __construct($prefix=null, $model=null, $singleKey='item', $multipleKey='items', $transformer='\PhalconRest\Transformer\Model', $controller='\PhalconRest\Mvc\Controller\Resource')
    {
        if($prefix){
            $this->setPrefix($prefix);
        }

        $this->model($model);
        $this->singleKey($singleKey);
        $this->multipleKey($multipleKey);

        $this->transformer($transformer);
        $this->controller($controller);

        $this->endpoints = [];

        return $this;
    }


    public function name($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }


    public function prefix($prefix)
    {
        $this->setPrefix($prefix);
        return $this;
    }

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
            $modelsMetaData = Di::getDefault()->get(Services::MODELS_METADATA);

            $modelClass = $this->getModel();

            $this->_modelPrimaryKey = $modelsMetaData->getIdentityField(new $modelClass);
        }

        return $this->_modelPrimaryKey;
    }


    public function transformer($transformer)
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

    public function controller($controller)
    {
        $this->controller = $controller;

        if($controller){

            $controller = new $controller();

            if($controller instanceof \PhalconRest\Mvc\Controller\Resource){
                $controller->setResource($this);
            }

            $this->setHandler($controller);
        }

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

    public function endpoint($name, Endpoint $endpoint)
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

    public function singleKey($singleKey)
    {
        $this->singleKey = $singleKey;
        return $this;
    }


    public function getMultipleKey()
    {
        return $this->multipleKey;
    }

    public function multipleKey($multipleKey)
    {
        $this->multipleKey = $multipleKey;
        return $this;
    }


    public static function create($prefix=null, $model=null, $singleKey='item', $multipleKey='items', $transformer='\PhalconRest\Transformer\Model', $controller='\PhalconRest\Mvc\Controller\Resource')
    {
        return new Resource($prefix, $model, $singleKey, $multipleKey, $transformer, $controller);
    }

}