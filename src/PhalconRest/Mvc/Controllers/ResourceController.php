<?php

namespace PhalconRest\Mvc\Controllers;

use PhalconRest\Api\ApiResource as ApiResource;
use PhalconRest\Transformers\ModelTransformer;

class ResourceController extends CollectionController
{
    protected function createResourceCollectionResponse($collection, $meta = null)
    {
        return $this->createCollectionResponse($collection, $this->getTransformer(),
            $this->getResource()->getCollectionKey(),
            $meta);
    }

    protected function getTransformer()
    {
        $transformerClass = $this->getResource()->getTransformer();
        $transformer = new $transformerClass();

        if ($transformer instanceof ModelTransformer) {
            $transformer->setModelClass($this->getResource()->getModel());
        }

        return $transformer;
    }

    /**
     * @return ApiResource
     */
    public function getResource()
    {
        $collection = $this->getCollection();
        if ($collection instanceof ApiResource) {
            return $collection;
        }

        return null;
    }

    protected function createResourceResponse($item, $meta = null)
    {
        return $this->createItemResponse($item, $this->getTransformer(), $this->getResource()->getItemKey(), $meta);
    }

    protected function createResourceOkResponse($item, $meta = null)
    {
        return $this->createItemOkResponse($item, $this->getTransformer(), $this->getResource()->getItemKey(), $meta);
    }
}
