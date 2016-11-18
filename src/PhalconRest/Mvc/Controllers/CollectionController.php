<?php

namespace PhalconRest\Mvc\Controllers;

class CollectionController extends FractalController
{
    /** @var \PhalconRest\Api\ApiCollection */
    protected $_collection;

    /** @var \PhalconRest\Api\ApiEndpoint */
    protected $_endpoint;

    /**
     * @return \PhalconRest\Api\ApiCollection
     */
    public function getCollection()
    {
        if (!$this->_collection) {
            $this->_collection = $this->application->getMatchedCollection();
        }

        return $this->_collection;
    }

    /**
     * @return \PhalconRest\Api\ApiEndpoint
     */
    public function getEndpoint()
    {
        if (!$this->_endpoint) {
            $this->_endpoint = $this->application->getMatchedEndpoint();
        }

        return $this->_endpoint;
    }
}
