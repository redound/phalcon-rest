<?php

namespace PhalconRest\Api;

use Phalcon\Acl;
use Phalcon\Di;
use PhalconRest\Constants\Services;
use PhalconRest\Transformers\ModelTransformer;
use PhalconRest\Mvc\Controllers\CrudResourceController;

class Resource extends Collection implements \PhalconRest\Acl\MountableInterface
{
    protected $model;
    protected $transformer;

    protected $singleKey = 'item';
    protected $multipleKey = 'items';

    protected $_modelPrimaryKey;

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
     */
    public function singleKey($singleKey)
    {
        $this->singleKey = $singleKey;
        return $this;
    }

    /**
     * @return string Response key for single item
     */
    public function getSingleKey()
    {
        return $this->singleKey;
    }

    /**
     * @param string $multipleKey Response key for multiple items
     *
     * @return static
     */
    public function multipleKey($multipleKey)
    {
        $this->multipleKey = $multipleKey;
        return $this;
    }

    /**
     * @return string Response key for multiple items
     */
    public function getMultipleKey()
    {
        return $this->multipleKey;
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
        $resource = new Resource($prefix);

        $resource
            ->singleKey('item')
            ->multipleKey('items')
            ->transformer(ModelTransformer::class)
            ->setHandler(CrudResourceController::class, true);

        if ($name) {
            $resource->name($name);
        }

        return $resource;
    }

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
            ->endpoint(Endpoint::all())
            ->endpoint(Endpoint::find())
            ->endpoint(Endpoint::create())
            ->endpoint(Endpoint::update())
            ->endpoint(Endpoint::remove());
    }
}