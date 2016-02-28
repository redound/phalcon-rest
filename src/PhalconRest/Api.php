<?php

namespace PhalconRest;

use PhalconRest\Api\Endpoint;
use PhalconRest\Api\Resource;
use PhalconRest\Constants\Services;

/**
 * Class Api
 * @package PhalconRest
 *
 * @property \PhalconRest\Api $application
 * @property \PhalconRest\Http\Request $request
 * @property \PhalconRest\Http\Response $response
 * @property \Phalcon\Acl\AdapterInterface $acl
 * @property \PhalconRest\Auth\Manager $authManager
 * @property \PhalconRest\User\Service $userService
 * @property \PhalconRest\Auth\TokenParserInterface $tokenParser
 * @property \PhalconRest\Data\Query $query
 * @property \PhalconRest\Data\Query\QueryParsers\PhqlQueryParser $phqlQueryParser
 * @property \PhalconRest\Data\Query\QueryParsers\UrlQueryParser $urlQueryParser
 */
class Api extends \Phalcon\Mvc\Micro
{
    protected $matchedRouteNameParts = null;
    protected $resourcesByIdentifier = [];
    protected $resourcesByName = [];
    protected $endpointsByIdentifier = [];

    /**
     * @return Resource[]
     */
    public function getResources()
    {
        return array_values($this->resourcesByIdentifier);
    }

    /**
     * @param $name
     * @return Resource|null
     */
    public function getResource($name)
    {
        return array_key_exists($name, $this->resourcesByName) ? $this->resourcesByName[$name] : null;
    }

    /**
     * @param \PhalconRest\Api\Resource $resource
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

            $this->resourcesByIdentifier[$collection->getIdentifier()] = $collection;

            /** @var Endpoint $endpoint */
            foreach ($collection->getEndpoints() as $endpoint) {

                $fullIdentifier = $collection->getIdentifier() . ' ' . $endpoint->getIdentifier();
                $this->endpointsByIdentifier[$fullIdentifier] = $endpoint;
            }
        }

        return parent::mount($collection);
    }

    /**
     * Attaches middleware to the API
     *
     * @param $middleware
     *
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

    /**
     * @return \PhalconRest\Api\Resource|null  The matched resource
     */
    public function getMatchedResource()
    {
        $resourceIdentifier = $this->getMatchedRouteNamePart('resource');

        if (!$resourceIdentifier) {
            return null;
        }

        return array_key_exists($resourceIdentifier,
            $this->resourcesByIdentifier) ? $this->resourcesByIdentifier[$resourceIdentifier] : null;
    }

    /**
     * @return \PhalconRest\Api\Endpoint|null  The matched endpoint
     */
    public function getMatchedEndpoint()
    {
        $resourceIdentifier = $this->getMatchedRouteNamePart('resource');
        $endpointIdentifier = $this->getMatchedRouteNamePart('endpoint');

        if (!$endpointIdentifier) {
            return null;
        }

        $fullIdentifier = $resourceIdentifier . ' ' . $endpointIdentifier;

        return array_key_exists($fullIdentifier,
            $this->endpointsByIdentifier) ? $this->endpointsByIdentifier[$fullIdentifier] : null;
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
}