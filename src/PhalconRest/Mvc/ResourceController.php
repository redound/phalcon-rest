<?php

namespace PhalconRest\Mvc;

class ResourceController extends FractalController
{
    /** @var \PhalconRest\Api\Resource */
    protected $resource;

    public function fetchList($resourceKey)
    {
        $this->_attachResource($resourceKey);

        $transformer = $this->resource->getTransformer();

        // Get Query parse & it to phqlQuery
        $phqlBuilder = $this->phqlQueryParser->fromQuery($this->query);
        $results = $phqlBuilder->getQuery()->execute();

        return $this->respondCollection($results, new $transformer, $this->resource->getKey());
    }

    public function fetchSingle($resourceKey, $id)
    {
        $this->_attachResource($resourceKey);

        $transformer = $this->resource->getTransformer();

        // Get Query parse & it to phqlQuery
        $phqlBuilder = $this->phqlQueryParser->fromQuery($this->query);

        $modelPrimaryKey = $this->resource->getPrimaryKey();
        $phqlBuilder->andWhere($modelPrimaryKey . ' = :id:', ['id' => $id]);

        $results = $phqlBuilder->getQuery()->execute();

        $firstResult = count($results) > 0 ? $results->getFirst() : null;

        return $this->respondCollection($firstResult, new $transformer, $this->resource->getKey());
    }

    public function create($resourceKey)
    {
        $data = (array)$this->request->getJsonRawBody();
    }

    public function update($resourceKey, $id)
    {
        $data = (array)$this->request->getJsonRawBody();
    }

    public function remove($resourceKey, $id)
    {

    }


    protected function _attachResource($resourceKey)
    {
        $this->resource = $this->apiService->getResource($resourceKey);
        $model = $this->resource->getModel();

        $this->query->setModel($model);
    }
}