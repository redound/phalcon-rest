<?php

namespace PhalconRest\Mvc\Controllers;

class ResourceController extends FractalController
{
    /** @var \PhalconRest\Api\Resource */
    protected $_resource;

    /** @var \PhalconRest\Api\Endpoint */
    protected $_endpoint;

    /**
     * @return \PhalconRest\Api\Resource
     */
    public function getResource()
    {
        if(!$this->_resource){
            $this->_resource = $this->application->getMatchedResource();
        }

        return $this->_resource;
    }

    /**
     * @return \PhalconRest\Api\Endpoint
     */
    public function getEndpoint()
    {
        if(!$this->_endpoint){
            $this->_endpoint = $this->application->getMatchedEndpoint();
        }

        return $this->_endpoint;
    }

    protected function getUser()
    {
        return $this->userService->getDetails();
    }


    protected function createResourceCollectionResponse($collection, $meta = null)
    {
        return $this->createCollectionResponse($collection, $this->getTransformer(), $this->getResource()->getMultipleKey(),
            $meta);
    }

    protected function getTransformer()
    {
        $transformerClass = $this->getResource()->getTransformer();
        $transformer = new $transformerClass();

        if ($transformer instanceof \PhalconRest\Transformers\ModelTransformer) {
            $transformer->setModelClass($this->getResource()->getModel());
        }

        return $transformer;
    }

    protected function createResourceResponse($item, $meta = null)
    {
        return $this->createItemResponse($item, $this->getTransformer(), $this->getResource()->getSingleKey(), $meta);
    }

    protected function createResourceOkResponse($item, $meta = null)
    {
        return $this->createItemOkResponse($item, $this->getTransformer(), $this->getResource()->getSingleKey(), $meta);
    }
}