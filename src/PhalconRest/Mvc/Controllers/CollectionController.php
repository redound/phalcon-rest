<?php

namespace PhalconRest\Mvc\Controllers;

class CollectionController extends FractalController
{
    /** @var \PhalconRest\Api\Collection */
    protected $_collection;

    /** @var \PhalconRest\Api\Endpoint */
    protected $_endpoint;

    /**
     * @return \PhalconRest\Api\Resource
     */
    public function getCollection()
    {
        if (!$this->_collection) {
            $this->_collection = $this->application->getMatchedCollection();
        }

        return $this->_collection;
    }

    /**
     * @return \PhalconRest\Api\Endpoint
     */
    public function getEndpoint()
    {
        if (!$this->_endpoint) {
            $this->_endpoint = $this->application->getMatchedEndpoint();
        }

        return $this->_endpoint;
    }
}