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

    /**
     * @param string[] $endpoints  Enabled CRUD endpoints
     *
     * @return static
     */
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

    /**
     * @param Endpoint|null $endpoint  New all endpoint
     *
     * @return static
     */
    public function allEndpoint(Endpoint $endpoint=null)
    {
        $this->endpoint(self::ALL, $endpoint ?: new \PhalconRest\Api\Endpoint\All());

        return $this;
    }

    public function getAllEndpoint()
    {
        return $this->getEndpoint(self::ALL);
    }

    /**
     * @param Endpoint|null $endpoint  New find endpoint
     *
     * @return static
     */
    public function findEndpoint(Endpoint $endpoint=null)
    {
        $this->endpoint(self::FIND, $endpoint ?: new \PhalconRest\Api\Endpoint\Find());

        return $this;
    }

    public function getFindEndpoint()
    {
        return $this->getEndpoint(self::FIND);
    }

    /**
     * @param Endpoint|null $endpoint  New create endpoint
     *
     * @return static
     */
    public function createEndpoint(Endpoint $endpoint=null)
    {
        $this->endpoint(self::CREATE, $endpoint ?: new \PhalconRest\Api\Endpoint\Create());

        return $this;
    }

    public function getCreateEndpoint()
    {
        return $this->getEndpoint(self::CREATE);
    }

    /**
     * @param Endpoint|null $endpoint  New update endpoint
     *
     * @return static
     */
    public function updateEndpoint(Endpoint $endpoint=null)
    {
        $this->endpoint(self::UPDATE, $endpoint ?: new \PhalconRest\Api\Endpoint\Update());

        return $this;
    }

    public function getUpdateEndpoint()
    {
        return $this->getEndpoint(self::UPDATE);
    }

    /**
     * @param Endpoint|null $endpoint  New delete endpoint
     *
     * @return static
     */
    public function deleteEndpoint(Endpoint $endpoint=null)
    {
        $this->endpoint(self::DELETE, $endpoint ?: new \PhalconRest\Api\Endpoint\Delete());

        return $this;
    }

    public function getDeleteEndpoint()
    {
        return $this->getEndpoint(self::DELETE);
    }

    /**
     * @param string $path
     * @param string $model
     * @param string $singleKey
     * @param string $multipleKey
     * @param array $crudEndpoints
     * @param string $transformer
     * @param string $controller
     *
     * @return static
     */
    public static function create($path=null, $model=null, $singleKey='item', $multipleKey='items', $crudEndpoints=self::NO_ENDPOINTS, $transformer='\PhalconRest\Transformer\Model', $controller='\PhalconRest\Mvc\Controller\ResourceCrud')
    {
        return new Crud($path, $model, $singleKey, $multipleKey, $crudEndpoints, $transformer, $controller);
    }
}