<?php

namespace PhalconRest;

use PhalconRest\Api\Collection;
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
    protected $collectionsByIdentifier = [];
    protected $collectionsByName = [];
    protected $endpointsByIdentifier = [];

    /**
     * @return Collection[]
     */
    public function getCollections()
    {
        return array_values($this->collectionsByIdentifier);
    }

    /**
     * @param $name
     *
     * @return Collection|null
     */
    public function getCollection($name)
    {
        return array_key_exists($name, $this->collectionsByName) ? $this->collectionsByName[$name] : null;
    }

    /**
     * @param \PhalconRest\Api\Resource $resource
     *
     * @return static
     * @throws Exception
     */
    public function resource(\PhalconRest\Api\Resource $resource)
    {
        $this->mount($resource);

        return $this;
    }

    /**
     * @param \PhalconRest\Api\Collection $collection
     *
     * @return static
     * @throws Exception
     */
    public function collection(\PhalconRest\Api\Collection $collection)
    {
        $this->mount($collection);

        return $this;
    }

    public function mount(\Phalcon\Mvc\Micro\CollectionInterface $collection)
    {
        if ($collection instanceof Collection) {

            $collectionName = $collection->getName();
            if (!is_null($collectionName)) {
                $this->collectionsByName[$collectionName] = $collection;
            }

            $this->collectionsByIdentifier[$collection->getIdentifier()] = $collection;

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
     * @return \PhalconRest\Api\Collection|null  The matched collection
     */
    public function getMatchedCollection()
    {
        $collectionIdentifier = $this->getMatchedRouteNamePart('collection');

        if (!$collectionIdentifier) {
            return null;
        }

        return array_key_exists($collectionIdentifier,
            $this->collectionsByIdentifier) ? $this->collectionsByIdentifier[$collectionIdentifier] : null;
    }

    /**
     * @return \PhalconRest\Api\Endpoint|null  The matched endpoint
     */
    public function getMatchedEndpoint()
    {
        $collectionIdentifier = $this->getMatchedRouteNamePart('collection');
        $endpointIdentifier = $this->getMatchedRouteNamePart('endpoint');

        if (!$endpointIdentifier) {
            return null;
        }

        $fullIdentifier = $collectionIdentifier . ' ' . $endpointIdentifier;

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