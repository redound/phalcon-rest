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

        $this->addCrudEndpoints($crudEndpoints);
    }

    public function addCrudEndpoints($endpoints)
    {
        if(in_array(self::ALL, $endpoints)){
            $this->addAllEndpoint();
        }

        if(in_array(self::FIND, $endpoints)){
            $this->addFindEndpoint();
        }

        if(in_array(self::CREATE, $endpoints)){
            $this->addCreateEndpoint();
        }

        if(in_array(self::UPDATE, $endpoints)){
            $this->addUpdateEndpoint();
        }

        if(in_array(self::DELETE, $endpoints)){
            $this->addDeleteEndpoint();
        }

        return $this;
    }


    public function getAllEndpoint()
    {
        return $this->getEndpoint(self::ALL);
    }

    public function setAllEndpoint(Endpoint $endpoint)
    {
        $this->setEndpoint(self::ALL, $endpoint);
    }

    public function addAllEndpoint()
    {
        $this->setEndpoint(self::ALL, new \PhalconRest\Api\Endpoint\All());
    }


    public function getFindEndpoint()
    {
        return $this->getEndpoint(self::FIND);
    }

    public function setFindEndpoint(Endpoint $endpoint)
    {
        $this->setEndpoint(self::FIND, $endpoint);
    }

    public function addFindEndpoint()
    {
        $this->setEndpoint(self::FIND, new \PhalconRest\Api\Endpoint\Find());
    }


    public function getCreateEndpoint()
    {
        return $this->getEndpoint(self::CREATE);
    }

    public function setCreateEndpoint(Endpoint $endpoint)
    {
        $this->setEndpoint(self::CREATE, $endpoint);
    }

    public function addCreateEndpoint()
    {
        $this->setEndpoint(self::CREATE, new \PhalconRest\Api\Endpoint\Create());
    }


    public function getUpdateEndpoint()
    {
        return $this->getEndpoint(self::UPDATE);
    }

    public function setUpdateEndpoint(Endpoint $endpoint)
    {
        $this->setEndpoint(self::UPDATE, $endpoint);
    }

    public function addUpdateEndpoint()
    {
        $this->setEndpoint(self::UPDATE, new \PhalconRest\Api\Endpoint\Update());
    }


    public function getDeleteEndpoint()
    {
        return $this->getEndpoint(self::DELETE);
    }

    public function setDeleteEndpoint(Endpoint $endpoint)
    {
        $this->setEndpoint(self::DELETE, $endpoint);
    }

    public function addDeleteEndpoint()
    {
        $this->setEndpoint(self::DELETE, new \PhalconRest\Api\Endpoint\Delete());
    }
}