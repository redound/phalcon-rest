<?php

namespace PhalconRest\Api\Resource;

use PhalconRest\Api\Endpoint;
use PhalconRest\Api\Resource;

class Crud extends Resource
{
    const ALL = 'all';
    const FIND = 'find';
    const CREATE = 'create';
    const UPDATE = 'update';
    CONST DELETE = 'delete';

    const NO_ENDPOINTS = [];
    const ALL_ENDPOINTS = [self::ALL, self::FIND, self::CREATE, self::UPDATE, self::DELETE];


    public function __construct($path=null, $model=null, $singleKey='item', $multipleKey='items', $crudEndpoints=self::NO_ENDPOINTS, $transformer='\PhalconRest\Transformer\Model', $controller='\PhalconRest\Mvc\Controller\ResourceCrud')
    {
        parent::__construct($path, $model, $singleKey, $multipleKey, $transformer, $controller);

        $this->crudEndpoints($crudEndpoints);
    }

    public function crudEndpoints($endpoints)
    {
        if(in_array(self::ALL, $endpoints)){
            $this->allEndpoint();
        }

        if(in_array(self::FIND, $endpoints)){
            $this->findEndpoint();
        }

        if(in_array(self::CREATE, $endpoints)){
            $this->createEndpoint();
        }

        if(in_array(self::UPDATE, $endpoints)){
            $this->updateEndpoint();
        }

        if(in_array(self::DELETE, $endpoints)){
            $this->deleteEndpoint();
        }

        return $this;
    }


    public function getAllEndpoint()
    {
        return $this->getEndpoint(self::ALL);
    }

    public function allEndpoint(Endpoint $endpoint=null)
    {
        $this->endpoint(self::ALL, $endpoint ?: new \PhalconRest\Api\Endpoint\All());
    }


    public function getFindEndpoint()
    {
        return $this->getEndpoint(self::FIND);
    }

    public function findEndpoint(Endpoint $endpoint=null)
    {
        $this->endpoint(self::FIND, $endpoint ?: new \PhalconRest\Api\Endpoint\Find());
    }


    public function getCreateEndpoint()
    {
        return $this->getEndpoint(self::CREATE);
    }

    public function createEndpoint(Endpoint $endpoint=null)
    {
        $this->endpoint(self::CREATE, $endpoint ?: new \PhalconRest\Api\Endpoint\Create());
    }


    public function getUpdateEndpoint()
    {
        return $this->getEndpoint(self::UPDATE);
    }

    public function updateEndpoint(Endpoint $endpoint=null)
    {
        $this->endpoint(self::UPDATE, $endpoint ?: new \PhalconRest\Api\Endpoint\Update());
    }


    public function getDeleteEndpoint()
    {
        return $this->getEndpoint(self::DELETE);
    }

    public function deleteEndpoint(Endpoint $endpoint=null)
    {
        $this->endpoint(self::DELETE, $endpoint ?: new \PhalconRest\Api\Endpoint\Delete());
    }


    public static function create($path=null, $model=null, $singleKey='item', $multipleKey='items', $crudEndpoints=self::NO_ENDPOINTS, $transformer='\PhalconRest\Transformer\Model', $controller='\PhalconRest\Mvc\Controller\ResourceCrud')
    {
        return new Crud($path, $model, $singleKey, $multipleKey, $crudEndpoints, $transformer, $controller);
    }
}