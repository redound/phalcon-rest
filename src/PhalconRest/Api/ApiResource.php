<?php

namespace PhalconRest\Api;

use Phalcon\Di;
use Phalcon\Mvc\Micro\CollectionInterface;
use PhalconApi\Acl\MountableInterface;
use PhalconRest\Constants\Services;
use PhalconRest\Mvc\Controllers\CrudResourceController;
use PhalconRest\Transformers\ModelTransformer;

class ApiResource extends ApiCollection implements MountableInterface, CollectionInterface
{
    protected $model;
    protected $transformer;

    protected $itemKey;
    protected $collectionKey;

    protected $_modelPrimaryKey;

    /**
     * Returns resource with default values & all, find, create, update and delete endpoints pre-configured
     *
     * @param string $prefix Prefix for the resource (e.g. /user)
     * @param string $name Name for the resource (e.g. users) (optional)
     *
     * @return static
     */
    public static function crud($prefix, $name = null)
    {
        return self::factory($prefix, $name)
            ->endpoint(ApiEndpoint::all())
            ->endpoint(ApiEndpoint::find())
            ->endpoint(ApiEndpoint::create())
            ->endpoint(ApiEndpoint::update())
            ->endpoint(ApiEndpoint::remove());
    }

    /**
     * Returns resource with default values
     *
     * @param string $prefix Prefix for the resource (e.g. /user)
     * @param string $name Name for the resource (e.g. users) (optional)
     *
     * @return static
     */
    public static function factory($prefix, $name = null)
    {
        $calledClass = get_called_class();

        /** @var \PhalconRest\Api\ApiResource $resource */
        $resource = new $calledClass($prefix);

        if (!$resource->getItemKey()) {
            $resource->itemKey('items');
        }

        if (!$resource->getCollectionKey()) {
            $resource->collectionKey('items');
        }

        if (!$resource->getTransformer()) {
            $resource->transformer(ModelTransformer::class);
        }

        if (!$resource->getHandler()) {
            $resource->setHandler(CrudResourceController::class);
        }

        if (!$resource->getName() && $name) {
            $resource->name($name);
        }

        if ($name) {
            $resource->name($name);
        }

        return $resource;
    }

    /**
     * @param string $model Classname of the model
     *
     * @return static
     */
    public function model($model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return string|null Classname of the model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return string|null Primary key of the model
     */
    public function getModelPrimaryKey()
    {
        if (!$this->model) {
            return null;
        }

        if (!$this->_modelPrimaryKey) {

            /** @var \Phalcon\Mvc\Model\MetaData $modelsMetaData */
            $modelsMetaData = Di::getDefault()->get(Services::MODELS_METADATA);

            $modelClass = $this->model;

            $this->_modelPrimaryKey = $modelsMetaData->getIdentityField(new $modelClass);
        }

        return $this->_modelPrimaryKey;
    }

    /**
     * @param string $transformer Classname of the transformer
     *
     * @return static
     */
    public function transformer($transformer)
    {
        $this->transformer = $transformer;
        return $this;
    }

    /**
     * @return string|null Classname of the transformer
     */
    public function getTransformer()
    {
        return $this->transformer;
    }

    /**
     * @param string $singleKey Response key for single item
     *
     * @return static
     *
     * @deprecated Use itemKey() instead
     */
    public function singleKey($singleKey)
    {
        return $this->itemKey($singleKey);
    }

    /**
     * @param string $itemKey Response key for single item
     *
     * @return static
     */
    public function itemKey($itemKey)
    {
        $this->itemKey = $itemKey;
        return $this;
    }

    /**
     * @return string Response key for single item
     *
     * @deprecated Use getItemKey() instead
     */
    public function getSingleKey()
    {
        return $this->getItemKey();
    }

    /**
     * @return string Response key for single item
     */
    public function getItemKey()
    {
        return ($this->itemKey ?: $this->name) ?: 'item';
    }

    /**
     * @param string $multipleKey Response key for multiple items
     *
     * @return static
     *
     * @deprecated Use collectionKey() instead
     */
    public function multipleKey($multipleKey)
    {
        return $this->collectionKey($multipleKey);
    }

    /**
     * @param string $collectionKey Response key for multiple items
     *
     * @return static
     */
    public function collectionKey($collectionKey)
    {
        $this->collectionKey = $collectionKey;
        return $this;
    }

    /**
     * @return string Response key for multiple items
     *
     * @deprecated Use getCollectionKey() instead
     */
    public function getMultipleKey()
    {
        return $this->getCollectionKey();
    }

    /**
     * @return string Response key for multiple items
     */
    public function getCollectionKey()
    {
        return ($this->collectionKey ?: $this->name) ?: 'items';
    }
}
