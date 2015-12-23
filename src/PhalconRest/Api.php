<?php

namespace PhalconRest;

use PhalconRest\Api\Endpoint;
use PhalconRest\Api\Resource;
use PhalconRest\Constants\ErrorCodes;
use PhalconRest\Constants\Services;
use PhalconRest\Exceptions\Exception;

class Api extends \Phalcon\Mvc\Micro
{
    protected $resources = [];
    protected $endpoints = [];

    /**
     * @return Resource[]
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * @param $name
     *
     * @return Resource
     */
    public function getResource($name)
    {
        return array_key_exists($name, $this->resources) ? $this->resources[$name] : null;
    }

    /**
     * @param string $name
     * @param Resource $resource
     *
     * @return static
     * @throws Exception
     */
    public function resource($name, Resource $resource)
    {
        $resource->name($name);
        $this->mount($resource);

        return $this;
    }

    public function mount(\Phalcon\Mvc\Micro\CollectionInterface $collection)
    {
        if($collection instanceof Resource){

            $resourceName = $collection->getName();
            if(!$resourceName){
                throw new Exception(ErrorCodes::GEN_SYSTEM, 'No name provided for resource');
            }

            $this->resources[$resourceName] = $collection;

            /** @var Endpoint $endpoint */
            foreach($collection->getEndpoints() as $endpoint){

                $fullEndpointName = $resourceName . ' ' . $endpoint->getName();
                $this->endpoints[$fullEndpointName] = $endpoint;
            }
        }

        return parent::mount($collection);
    }

    /**
     * @return Resource|null  The matched resource
     */
    public function getMatchedResource()
    {
        $routeName = $this->getRouter()->getMatchedRoute()->getName();
        if(!$routeName){
            return null;
        }

        $routeNameParts = explode(' ', $routeName);

        if(count($routeNameParts) != 2){
            return null;
        }

        $resourceName = $routeNameParts[0];

        return array_key_exists($resourceName, $this->resources) ? $this->resources[$resourceName] : null;
    }

    /**
     * @return Endpoint|null  The matched endpoint
     */
    public function getMatchedEndpoint()
    {
        $routeName = $this->getRouter()->getMatchedRoute()->getName();
        if(!$routeName){
            return null;
        }

        return array_key_exists($routeName, $this->endpoints) ? $this->endpoints[$routeName] : null;
    }

    /**
     * Attaches middleware to the API
     *
     * @param $middleware
     * @return static
     */
    public function attach($middleware)
    {
        if(!$this->getEventsManager()){
            $this->setEventsManager($this->getDI()->get(Services::EVENTS_MANAGER));
        }

        $this->getEventsManager()->attach('micro', $middleware);

        return $this;
    }
}