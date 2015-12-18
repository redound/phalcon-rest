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

    const ALL_ENDPOINTS = [self::ALL, self::FIND, self::CREATE, self::UPDATE, self::DELETE];


    public function __construct($path=null, $model=null, $singleKey='item', $multipleKey='items', $crudEndpoints=self::ALL_ENDPOINTS, $transformer='\PhalconRest\Transformer\Model', $controller='\PhalconRest\Mvc\Controller\ResourceCrud')
    {
        parent::__construct($path, $model, $singleKey, $multipleKey, $transformer, $controller);

        if(in_array(self::ALL, $crudEndpoints)){
            $this->setEndpoint(self::ALL, new \PhalconRest\Api\Endpoint\All());
        }

        if(in_array(self::FIND, $crudEndpoints)){
            $this->setEndpoint(self::FIND, new \PhalconRest\Api\Endpoint\Find());
        }

        if(in_array(self::CREATE, $crudEndpoints)){
            $this->setEndpoint(self::CREATE, new \PhalconRest\Api\Endpoint\Create());
        }

        if(in_array(self::UPDATE, $crudEndpoints)){
            $this->setEndpoint(self::UPDATE, new \PhalconRest\Api\Endpoint\Update());
        }

        if(in_array(self::DELETE, $crudEndpoints)){
            $this->setEndpoint(self::DELETE, new \PhalconRest\Api\Endpoint\Delete());
        }
    }

    public function getAllEndpoint()
    {
        return $this->getEndpoint(self::ALL);
    }

    public function setAllEndpoint(Endpoint $endpoint)
    {
        $this->setEndpoint(self::ALL, $endpoint);
    }


    public function getFindEndpoint()
    {
        return $this->getEndpoint(self::FIND);
    }

    public function setFindEndpoint(Endpoint $endpoint)
    {
        $this->setEndpoint(self::FIND, $endpoint);
    }


    public function getCreateEndpoint()
    {
        return $this->getEndpoint(self::CREATE);
    }

    public function setCreateEndpoint(Endpoint $endpoint)
    {
        $this->setEndpoint(self::CREATE, $endpoint);
    }


    public function getUpdateEndpoint()
    {
        return $this->getEndpoint(self::UPDATE);
    }

    public function setUpdateEndpoint(Endpoint $endpoint)
    {
        $this->setEndpoint(self::UPDATE, $endpoint);
    }


    public function getDeleteEndpoint()
    {
        return $this->getEndpoint(self::DELETE);
    }

    public function setDeleteEndpoint(Endpoint $endpoint)
    {
        $this->setEndpoint(self::DELETE, $endpoint);
    }
}