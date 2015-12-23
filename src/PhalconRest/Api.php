<?php

namespace PhalconRest;

use PhalconRest\Api\Endpoint;
use PhalconRest\Api\Resource;
use PhalconRest\Constants\Services;

class Api extends \Phalcon\Mvc\Micro
{
    protected $matchedRouteNameParts = null;
    protected $resourcesByPrefix = [];
    protected $resourcesByName = [];
    protected $endpointsByMethodPath = [];

    /**
     * @return Resource[]
     */
    public function getResources()
    {
        return array_values($this->resourcesByPrefix);
    }

    /**
     * @param $name
     *
     * @return Resource
     */
    public function getResource($name)
    {
        return array_key_exists($name, $this->resourcesByName) ? $this->resourcesByName[$name] : null;
    }

    /**
     * @param Resource $resource
     *
     * @return static
     * @throws Exception
     */
    public function resource(Resource $resource)
    {
        $this->mount($resource);

        return $this;
    }

    public function mount(\Phalcon\Mvc\Micro\CollectionInterface $collection)
    {
        if ($collection instanceof Resource) {

            $resourceName = $collection->getName();
            if (!is_null($resourceName)) {
                $this->resourcesByName[$resourceName] = $collection;
            }

            $this->resourcesByPrefix[$collection->getPrefix()] = $collection;

            /** @var Endpoint $endpoint */
            foreach($collection->getEndpoints() as $endpoint){
                $this->endpointsByMethodPath[$endpoint->getHttpMethod() . $endpoint->getPath()] = $endpoint;
            }
        }

        return parent::mount($collection);
    }

    /**
     * Attaches middleware to the API
     *
     * @param $middleware
     * @return static
     */
    public function attach($middleware)
    {
        if (!$this->getEventsManager()) {
            $this->setEventsManager($this->getDI()->get(Services::EVENTS_MANAGER));
        }

        $this->getEventsManager()->attach('micro', $middleware);

        return $this;
    }

    protected function getMatchedRouteNamePart($key)
    {
        if (is_null($this->matchedRouteNameParts)) {

            $routeName = $this->getRouter()->getMatchedRoute()->getName();

            if (!$routeName) {
                return null;
            }

            $this->matchedRouteNameParts = @unserialize($routeName);
        }

        if (is_array($this->matchedRouteNameParts) && array_key_exists($key, $this->matchedRouteNameParts)) {
            return $this->matchedRouteNameParts[$key];
        }

        return null;
    }

    /**
     * @return Resource|null  The matched resource
     */
    public function getMatchedResource()
    {
        $resourcePrefix = $this->getMatchedRouteNamePart('resourcePrefix');

        if (!$resourcePrefix) {
            return null;
        }

        return array_key_exists($resourcePrefix, $this->resourcesByPrefix) ? $this->resourcesByPrefix[$resourcePrefix] : null;
    }

    /**
     * @return Endpoint|null  The matched endpoint
     */
    public function getMatchedEndpoint()
    {
        $httpMethod = $this->getMatchedRouteNamePart('httpMethod');
        $endpointPath = $this->getMatchedRouteNamePart('endpointPath');

        if (!$httpMethod || !$endpointPath) {
            return null;
        }

        $endpointPath = $httpMethod . $endpointPath;

        return array_key_exists($endpointPath, $this->endpointsByMethodPath) ? $this->endpointsByMethodPath[$endpointPath] : null;
    }
}