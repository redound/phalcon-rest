<?php

namespace PhalconRest\Mvc\Controller;

class Resource extends \PhalconRest\Mvc\Controller\Fractal
{
    /** @var \PhalconRest\Api\Resource */
    protected $resource;

    public function setResource(\PhalconRest\Api\Resource $resource)
    {
        $this->resource = $resource;
        $this->query->setModel($resource->getModel());

        return $this;
    }


    protected function createResourceCollectionResponse($collection, $meta = null)
    {
        return $this->createCollectionResponse($collection, $this->getTransformer(), $this->resource->getMultipleKey(), $meta);
    }

    protected function createResourceResponse($item, $meta = null)
    {
        return $this->createItemResponse($item, $this->getTransformer(), $this->resource->getSingleKey(), $meta);
    }

    protected function createResourceOkResponse($item, $meta = null)
    {
        return $this->createItemOkResponse($item, $this->getTransformer(), $this->resource->getSingleKey(), $meta);
    }

    protected function getTransformer()
    {
        $transformerClass = $this->resource->getTransformer();
        $transformer = new $transformerClass();

        if($transformer instanceof \PhalconRest\Transformer\Model){
            $transformer->setModelClass($this->resource->getModel());
        }

        return $transformer;
    }
}