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
        $this->beforeAll();

        $response = $this->getAllResponse($this->getAllData());

        $this->afterAll($response);

        return $response;
    }

    protected function beforeAll()
    {
    }

    protected function afterAll($response)
    {
    }

    protected function getAllData()
    {
        $phqlBuilder = $this->phqlQueryParser->fromQuery($this->query);
        $phqlBuilder->from($this->getResource()->getModel());

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

        if (!$item) {
            return $this->onItemNotFound($id);
        }

        $response = $this->getFindResponse($item);

        $this->afterFind($response);

        return $response;
    }

    protected function beforeFind()
    {
    }

    protected function afterFind($response)
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

        if (!$item) {
            return $this->onCreateFailed($data);
        }

        $this->afterCreate($item, $data);

        return $this->getCreateResponse($item, $data);
    }

    protected function beforeCreate($data)
    {
    }

    protected function afterCreate(Model $createdItem, $data)
    {
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
        $data = $this->getPostedData();
        $item = $this->getItem($id);

        if (!$item) {
            return $this->onItemNotFound($id);
        }

        $this->beforeUpdate($item, $data);

        $item = $this->updateItem($item, $data);

        if (!$item) {
            return $this->onUpdateFailed($item, $data);
        }

        $this->afterUpdate($item, $data);

        return $this->getUpdateResponse($item, $data);
    }

    protected function beforeUpdate(Model $item, $data)
    {
    }

    protected function afterUpdate(Model $updatedItem, $data)
    {
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
        $item = $this->getItem($id);

        if (!$item) {
            return $this->onItemNotFound($id);
        }

        $this->beforeRemove($item);

        $success = $this->removeItem($item);

        if (!$success) {
            return $this->onRemoveFailed($item);
        }

        $this->afterRemove($item);

        return $this->getRemoveResponse($item);
    }

    protected function beforeRemove(Model $item)
    {
    }

    protected function afterRemove(Model $removedItem)
    {
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

    /*** ERROR HOOKS ***/

    protected function onItemNotFound($id)
    {
        throw new Exception(ErrorCodes::DATA_NOT_FOUND, 'Item was not found');
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