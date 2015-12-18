<?php
namespace PhalconRest\Mvc\Controller;

use Phalcon\Mvc\Model;
use PhalconRest\Api\Resource;
use PhalconRest\Constants\ErrorCodes;
use PhalconRest\Exceptions\Exception;

class ResourceCrud extends \PhalconRest\Mvc\Controller\Resource
{
    /*** ALL ***/

    public function all()
    {
        $this->beforeAll();

        $response = $this->getAllResponse($this->getAllData());

        $this->afterAll($response);

        return $response;
    }

    protected function beforeAll() {}

    protected function afterAll($response) {}

    protected function getAllData()
    {
        $phqlBuilder = $this->phqlQueryParser->fromQuery($this->query);
        $phqlBuilder->from($this->resource->getModel());

        return $phqlBuilder->getQuery()->execute();
    }

    protected function getAllResponse($data)
    {
        return $this->createResourceCollectionResponse($data);
    }


    /*** FIND ***/

    public function find($id)
    {
        $this->beforeFind();

        $item = $this->getFindData($id);

        if(!$item){
            return $this->onItemNotFound($id);
        }

        $response = $this->getFindResponse($item);

        $this->afterFind($response);

        return $response;
    }

    protected function beforeFind() {}

    protected function afterFind($response) {}

    protected function getFindData($id)
    {
        $phqlBuilder = $this->phqlQueryParser->fromQuery($this->query);

        $modelPrimaryKey = $this->resource->getModelPrimaryKey();
        $phqlBuilder
            ->from($this->resource->getModel())
            ->andWhere($modelPrimaryKey . ' = :id:', ['id' => $id])
            ->limit(1);

        $results = $phqlBuilder->getQuery()->execute();

        return count($results) >= 1 ? $results->getFirst() : null;
    }

    protected function getFindResponse(Model $item)
    {
        return $this->createResourceResponse($item);
    }


    /*** CREATE ***/

    public function create()
    {
        $data = $this->getPostedData();

        $this->beforeCreate($data);

        $item = $this->createItem($data);

        if(!$item){
            return $this->onCreateFailed($data);
        }

        $this->afterCreate($item, $data);

        return $this->getCreateResponse($item, $data);
    }

    protected function beforeCreate($data) {}

    protected function afterCreate(Model $createdItem, $data) {}

    /**
     * @param $data
     *
     * @return Model Created model
     */
    protected function createItem($data)
    {
        $modelClass = $this->resource->getModel();

        /** @var Model $item */
        $item = new $modelClass();
        $item->assign($data);

        $success = $item->create();

        return $success ? $item : null;
    }

    protected function getCreateResponse(Model $createdItem, $data)
    {
        return $this->createResourceOkResponse($createdItem);
    }


    /*** UPDATE ***/

    public function update($id)
    {
        $data = $this->getPostedData();
        $item = $this->getItem($id);

        if(!$item){
            return $this->onItemNotFound($id);
        }

        $this->beforeUpdate($item, $data);

        $item = $this->updateItem($item, $data);

        if(!$item){
            return $this->onUpdateFailed($item, $data);
        }

        $this->afterUpdate($item, $data);

        return $this->getUpdateResponse($item, $data);
    }

    protected function beforeUpdate(Model $item, $data) {}

    protected function afterUpdate(Model $updatedItem, $data) {}

    /**
     * @param Model $item
     * @param $data
     *
     * @return Model Updated model
     */
    protected function updateItem(Model $item, $data)
    {
        $item->assign($data);
        $success = $item->update();

        return $success ? $item : null;
    }

    protected function getUpdateResponse(Model $updatedItem, $data)
    {
        return $this->createResourceOkResponse($updatedItem);
    }


    /*** DELETE ***/

    public function delete($id)
    {
        $item = $this->getItem($id);

        if(!$item){
            return $this->onItemNotFound($id);
        }

        $this->beforeDelete($item);

        $success = $this->deleteItem($item);

        if(!$success){
            return $this->onDeleteFailed($item);
        }

        $this->afterDelete($item);

        return $this->getDeleteResponse($item);
    }

    protected function beforeDelete(Model $item) {}

    protected function afterDelete(Model $deletedItem) {}

    /**
     * @param Model $item
     *
     * @return bool Deletion succeeded/failed
     */
    protected function deleteItem(Model $item)
    {
        return $item->delete();
    }

    protected function getDeleteResponse(Model $deletedItem)
    {
        return $this->createOkResponse();
    }


    /*** GENERAL HOOKS ***/

    protected function getPostedData()
    {
        return (array)$this->request->getJsonRawBody();
    }

    /**
     * @param $id
     *
     * @return Model
     */
    protected function getItem($id)
    {
        $modelClass = $this->resource->getModel();
        return $modelClass::findFirst($id);
    }


    /*** ERROR HOOKS ***/

    protected function onItemNotFound($id)
    {
        throw new Exception(ErrorCodes::DATA_NOTFOUND, 'Item was not found');
    }

    protected function onCreateFailed($data)
    {
        throw new Exception(ErrorCodes::DATA_FAIL, 'Unable to create item');
    }

    protected function onUpdateFailed(Model $item, $data)
    {
        throw new Exception(ErrorCodes::DATA_FAIL, 'Unable to update item');
    }

    protected function onDeleteFailed(Model $item)
    {
        throw new Exception(ErrorCodes::DATA_FAIL, 'Unable to delete item');
    }
}