<?php

namespace PhalconRest\Mvc\Controllers;

class ResourceController extends CollectionController
{
    /**
     * @return \PhalconRest\Api\Resource
     */
    public function getResource()
    {
        $collection = $this->getCollection();
        if ($collection instanceof \PhalconRest\Api\Resource) {
            return $collection;
        }

        return null;
    }

    protected function createResourceCollectionResponse($collection, $meta = null)
    {
        return $this->createCollectionResponse($collection, $this->getTransformer(),
            $this->getResource()->getMultipleKey(),
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