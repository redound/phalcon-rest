<?php
namespace PhalconRest\Mvc\Controllers;

use Phalcon\Mvc\Model;
use PhalconRest\Constants\ErrorCodes;
use PhalconRest\Constants\PostedDataMethods;
use PhalconRest\Exception;

class CrudResourceController extends \PhalconRest\Mvc\Controllers\ResourceController
{
    /*** ALL ***/

    public function all()
    {
        $this->beforeHandle();
        $this->beforeHandleRead();
        $this->beforeHandleAll();

        $data = $this->getAllData();

        if (!$this->allAllowed($data)) {
            return $this->onNotAllowed();
        }

        $response = $this->getAllResponse($data);

        $this->afterHandleAll($data, $response);
        $this->afterHandleRead();
        $this->afterHandle();

        return $response;
    }

    protected function beforeHandleAll()
    {
    }

    protected function afterHandleAll($data, $response)
    {
    }

    protected function modifyAllQuery(\Phalcon\Mvc\Model\Query\Builder $query)
    {
    }

    protected function getAllData()
    {
        $phqlBuilder = $this->phqlQueryParser->fromQuery($this->query);
        $phqlBuilder->from($this->getResource()->getModel());

        $this->modifyReadQuery($phqlBuilder);
        $this->modifyAllQuery($phqlBuilder);

        return $phqlBuilder->getQuery()->execute();
    }

    protected function allAllowed($data)
    {
        return true;
    }

    protected function getAllResponse($data)
    {
        return $this->createResourceCollectionResponse($data);
    }

    /*** FIND ***/

    public function find($id)
    {
        $this->beforeHandle();
        $this->beforeHandleRead();
        $this->beforeHandleFind($id);

        $item = $this->getFindData($id);

        if (!$item) {
            return $this->onItemNotFound($id);
        }

        if (!$this->findAllowed($id, $item)) {
            return $this->onNotAllowed();
        }

        $response = $this->getFindResponse($item);

        $this->afterHandleFind($item, $response);
        $this->beforeHandleRead();
        $this->afterHandle();

        return $response;
    }

    protected function beforeHandleFind($id)
    {
    }

    protected function afterHandleFind(Model $item, $response)
    {
    }

    protected function modifyFindQuery(\Phalcon\Mvc\Model\Query\Builder $query, $id)
    {
    }

    protected function getFindData($id)
    {
        $phqlBuilder = $this->phqlQueryParser->fromQuery($this->query);

        $modelPrimaryKey = $this->getResource()->getModelPrimaryKey();
        $phqlBuilder
            ->from($this->getResource()->getModel())
            ->andWhere($modelPrimaryKey . ' = :id:', ['id' => $id])
            ->limit(1);

        $this->modifyReadQuery($phqlBuilder);
        $this->modifyFindQuery($phqlBuilder, $id);

        $results = $phqlBuilder->getQuery()->execute();

        return count($results) >= 1 ? $results->getFirst() : null;
    }

    protected function findAllowed($id, Model $item)
    {
        return true;
    }

    protected function getFindResponse(Model $item)
    {
        return $this->createResourceResponse($item);
    }

    protected function modifyReadQuery(\Phalcon\Mvc\Model\Query\Builder $query)
    {
    }

    /*** CREATE ***/

    public function create()
    {
        $this->beforeHandle();
        $this->beforeHandleWrite();
        $this->beforeHandleCreate();

        $data = $this->getPostedData();

        if (!$this->saveAllowed($data) || !$this->createAllowed($data)) {
            return $this->onNotAllowed();
        }

        if (!$data || count($data) == 0) {
            return $this->onNoDataProvided();
        }

        $item = $this->createItem($data);

        if (!$item) {
            return $this->onCreateFailed($data);
        }

        $response = $this->getCreateResponse($item, $data);

        $this->afterHandleCreate($item, $data, $response);
        $this->afterHandleWrite();
        $this->afterHandle();

        return $response;
    }

    protected function beforeHandleCreate()
    {
    }

    protected function afterHandleCreate(Model $createdItem, $data, $response)
    {
    }

    protected function createAllowed($data)
    {
        return true;
    }

    /**
     * @param $data
     *
     * @return Model Created model
     */
    protected function createItem($data)
    {
        $modelClass = $this->getResource()->getModel();

        /** @var Model $item */
        $item = new $modelClass();

        $this->beforeAssignData($item, $data);
        $item->assign($data);
        $this->afterAssignData($item, $data);

        $this->beforeSave($item);
        $this->beforeCreate($item);

        $success = $item->create();

        if ($success) {

            $this->afterCreate($item);
            $this->afterSave($item);
        }

        return $success ? $item : null;
    }

    protected function getCreateResponse(Model $createdItem, $data)
    {
        return $this->createResourceOkResponse($createdItem);
    }

    protected function beforeCreate(Model $item)
    {
    }

    protected function afterCreate(Model $item)
    {
    }

    /*** UPDATE ***/

    public function update($id)
    {
        $this->beforeHandle();
        $this->beforeHandleWrite();
        $this->beforeHandleUpdate($id);

        $data = $this->getPostedData();
        $item = $this->getItem($id);

        if (!$item) {
            return $this->onItemNotFound($id);
        }

        if (!$data || count($data) == 0) {
            return $this->onNoDataProvided();
        }

        if (!$this->saveAllowed($data) || !$this->updateAllowed($item, $data)) {
            return $this->onNotAllowed();
        }

        $item = $this->updateItem($item, $data);

        if (!$item) {
            return $this->onUpdateFailed($item, $data);
        }

        $response = $this->getUpdateResponse($item, $data);

        $this->afterHandleUpdate($item, $data, $response);
        $this->afterHandleWrite();
        $this->afterHandle();

        return $response;
    }

    protected function beforeHandleUpdate($id)
    {
    }

    protected function afterHandleUpdate(Model $updatedItem, $data, $response)
    {
    }

    protected function updateAllowed(Model $item, $data)
    {
        return true;
    }

    /**
     * @param Model $item
     * @param $data
     *
     * @return Model Updated model
     */
    protected function updateItem(Model $item, $data)
    {
        $this->beforeAssignData($item, $data);
        $item->assign($data);
        $this->afterAssignData($item, $data);

        $this->beforeSave($item);
        $this->beforeUpdate($item);

        $success = $item->update();

        if ($success) {

            $this->afterUpdate($item);
            $this->afterSave($item);
        }

        return $success ? $item : null;
    }

    protected function getUpdateResponse(Model $updatedItem, $data)
    {
        return $this->createResourceOkResponse($updatedItem);
    }

    protected function beforeUpdate(Model $item)
    {
    }

    protected function afterUpdate(Model $item)
    {
    }

    /*** REMOVE ***/

    public function remove($id)
    {
        $this->beforeHandle();
        $this->beforeHandleWrite();
        $this->beforeHandleRemove($id);

        $item = $this->getItem($id);

        if (!$item) {
            return $this->onItemNotFound($id);
        }

        if (!$this->removeAllowed($item)) {
            return $this->onNotAllowed();
        }

        $success = $this->removeItem($item);

        if (!$success) {
            return $this->onRemoveFailed($item);
        }

        $response = $this->getRemoveResponse($item);

        $this->afterHandleRemove($item, $response);
        $this->afterHandleWrite();
        $this->afterHandle();

        return $response;
    }

    protected function beforeHandleRemove($id)
    {
    }

    protected function afterHandleRemove(Model $removedItem, $response)
    {
    }

    protected function removeAllowed(Model $item)
    {
        return true;
    }

    /**
     * @param Model $item
     *
     * @return bool Remove succeeded/failed
     */
    protected function removeItem(Model $item)
    {
        $this->beforeRemove($item);

        $success = $item->delete();

        if ($success) {
            $this->afterRemove($item);
        }

        return $success;
    }

    protected function getRemoveResponse(Model $removedItem)
    {
        return $this->createOkResponse();
    }

    protected function beforeRemove(Model $item)
    {
    }

    protected function afterRemove(Model $item)
    {
    }

    /*** GENERAL HOOKS ***/

    protected function getPostedData()
    {
        $resourcePostedDataMode = $this->getResource()->getPostedDataMethod();
        $endpointPostedDataMode = $this->getEndpoint()->getPostedDataMethod();

        $postedDataMode = $resourcePostedDataMode;
        if ($endpointPostedDataMode != PostedDataMethods::AUTO) {
            $postedDataMode = $endpointPostedDataMode;
        }

        $postedData = null;

        switch ($postedDataMode) {

            case PostedDataMethods::POST:
                $postedData = $this->request->getPost();
                break;

            case PostedDataMethods::JSON_BODY:
                $postedData = $this->request->getJsonRawBody(true);
                break;

            case PostedDataMethods::AUTO:
            default:
                $postedData = $this->request->getPostedData();
        }

        return $postedData;
    }

    /**
     * @param $id
     *
     * @return Model
     */
    protected function getItem($id)
    {
        $modelClass = $this->getResource()->getModel();
        return $modelClass::findFirst($id);
    }

    protected function beforeHandle()
    {
    }

    protected function afterHandle()
    {
    }

    protected function beforeHandleRead()
    {
    }

    protected function afterHandleRead()
    {
    }

    protected function beforeHandleWrite()
    {
    }

    protected function afterHandleWrite()
    {
    }

    protected function beforeAssignData(Model $item, $data)
    {
    }

    protected function afterAssignData(Model $item, $data)
    {
    }

    protected function beforeSave(Model $item)
    {
    }

    protected function afterSave(Model $item)
    {
    }

    protected function saveAllowed($data)
    {
        return true;
    }

    /*** ERROR HOOKS ***/

    protected function onItemNotFound($id)
    {
        throw new Exception(ErrorCodes::DATA_NOT_FOUND, 'Item was not found');
    }

    protected function onNoDataProvided()
    {
        throw new Exception(ErrorCodes::POST_DATA_NOT_PROVIDED, 'No post-data provided');
    }

    protected function onNotAllowed()
    {
        throw new Exception(ErrorCodes::ACCESS_DENIED, 'Operation is not allowed');
    }

    protected function onCreateFailed($data)
    {
        throw new Exception(ErrorCodes::DATA_FAILED, 'Unable to create item');
    }

    protected function onUpdateFailed(Model $item, $data)
    {
        throw new Exception(ErrorCodes::DATA_FAILED, 'Unable to update item');
    }

    protected function onRemoveFailed(Model $item)
    {
        throw new Exception(ErrorCodes::DATA_FAILED, 'Unable to remove item');
    }
}