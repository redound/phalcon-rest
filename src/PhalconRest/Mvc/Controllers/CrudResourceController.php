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
        $this->beforeRead();
        $this->beforeAll();

        $data = $this->getAllData();

        if (!$this->allAllowed($data)) {
            return $this->onNotAllowed();
        }

        $response = $this->getAllResponse($data);

        $this->afterAll($data, $response);
        $this->afterRead();
        $this->afterHandle();

        return $response;
    }

    protected function beforeAll()
    {
    }

    protected function afterAll($data, $response)
    {
    }

    protected function modifyAllQuery(\Phalcon\Mvc\Model\Query\Builder $query)
    {
    }

    protected function getAllData()
    {
        $phqlBuilder = $this->phqlQueryParser->fromQuery($this->query);
        $phqlBuilder->from($this->getResource()->getModel());

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
        $this->beforeRead();
        $this->beforeFind($id);

        $item = $this->getFindData($id);

        if (!$item) {
            return $this->onItemNotFound($id);
        }

        if (!$this->findAllowed($id, $item)) {
            return $this->onNotAllowed();
        }

        $response = $this->getFindResponse($item);

        $this->afterFind($item, $response);
        $this->beforeRead();
        $this->afterHandle();

        return $response;
    }

    protected function beforeFind($id)
    {
    }

    protected function afterFind(Model $item, $response)
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

    /*** CREATE ***/

    public function create()
    {
        $this->beforeHandle();
        $this->beforeWrite();
        $this->beforeCreate();

        $data = $this->getPostedData();

        if (!$this->createAllowed($data)) {
            return $this->onNotAllowed();
        }

        $item = $this->createItem($data);

        if (!$item) {
            return $this->onCreateFailed($data);
        }

        $response = $this->getCreateResponse($item, $data);

        $this->afterCreate($item, $data, $response);
        $this->afterWrite();
        $this->afterHandle();

        return $response;
    }

    protected function beforeCreate()
    {
    }

    protected function afterCreate(Model $createdItem, $data, $response)
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
        $this->beforeHandle();
        $this->beforeWrite();
        $this->beforeUpdate($id);

        $data = $this->getPostedData();
        $item = $this->getItem($id);

        if (!$item) {
            return $this->onItemNotFound($id);
        }

        if (!$this->updateAllowed($item, $data)) {
            return $this->onNotAllowed();
        }

        $item = $this->updateItem($item, $data);

        if (!$item) {
            return $this->onUpdateFailed($item, $data);
        }

        $response = $this->getUpdateResponse($item, $data);

        $this->afterUpdate($item, $data, $response);
        $this->afterWrite();
        $this->afterHandle();

        return $response;
    }

    protected function beforeUpdate($id)
    {
    }

    protected function afterUpdate(Model $updatedItem, $data, $response)
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
        $item->assign($data);
        $success = $item->update();

        return $success ? $item : null;
    }

    protected function getUpdateResponse(Model $updatedItem, $data)
    {
        return $this->createResourceOkResponse($updatedItem);
    }

    /*** REMOVE ***/

    public function remove($id)
    {
        $this->beforeHandle();
        $this->beforeWrite();
        $this->beforeRemove($id);

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

        $this->afterRemove($item, $response);
        $this->afterWrite();
        $this->afterHandle();

        return $response;
    }

    protected function beforeRemove($id)
    {
    }

    protected function afterRemove(Model $removedItem, $response)
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
        return $item->delete();
    }

    protected function getRemoveResponse(Model $removedItem)
    {
        return $this->createOkResponse();
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

    protected function beforeRead()
    {
    }

    protected function afterRead()
    {
    }

    protected function beforeWrite()
    {
    }

    protected function afterWrite()
    {
    }


    /*** ERROR HOOKS ***/

    protected function onItemNotFound($id)
    {
        throw new Exception(ErrorCodes::DATA_NOT_FOUND, 'Item was not found');
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