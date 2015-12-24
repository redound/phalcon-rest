<?php

namespace PhalconRest\Export;

use Phalcon\Acl;

class Documentation extends \PhalconRest\Mvc\Plugin
{
    public $name;
    public $basePath;
    protected $routes = [];
    protected $resources = [];

    public function __construct($name, $basePath)
    {
        $this->name = $name;
        $this->basePath = $basePath;
    }

    public function importManyRoutes(array $routes)
    {
        /** @var \Phalcon\Mvc\Router\Route $route */
        foreach ($routes as $route) {
            $this->importRoute($route);
        }
    }

    public function importRoute(\Phalcon\Mvc\Router\Route $route)
    {
        if (@unserialize($route->getName())) {
            return;
        }

        $this->routes[] = $route;
    }

    public function importManyResources(array $resources)
    {
        /** @var \PhalconRest\Api\Resource $resource */
        foreach ($resources as $resource) {
            $this->importResource($resource);
        }
    }

    public function importResource(\PhalconRest\Api\Resource $details)
    {
        $aclRules = $details->getAclRules($this->acl->getRoles());

        $resource = new \PhalconRest\Export\Documentation\Resource($aclRules[Acl::ALLOW], $aclRules[Acl::DENY]);
        $resource->allowedRoles = $aclRules[Acl::ALLOW];
        $resource->deniedRoles = $aclRules[Acl::DENY];
        $resource->setDetails($details);

        if ($modelClass = $details->getModel()) {

            /** @var \PhalconRest\Mvc\Model $model */
            $model = new $modelClass;

            if (method_exists($model, 'getSource')) {
                $resource->setSource($model->getSource());
            }

            if (method_exists($model, 'columnMap')) {
                $resource->setColumnMap($model->columnMap());
            }

            if (method_exists($model, 'whitelist')) {
                $resource->setWhitelist($model->whitelist());
            }

            if ($transformerClass = $details->getTransformer()) {

                /** @var \PhalconRest\Transformers\ModelTransformer $transformer */
                $transformer = new $transformerClass;

                if ($transformer instanceof \PhalconRest\Transformers\ModelTransformer) {

                    $transformer->setModelClass($modelClass);
                    $reflectionClass = new \ReflectionClass($transformer);
                    $constants = $reflectionClass->getConstants();
                    $reversedConstants = array_flip($constants);
                    $dataTypes = $transformer->getModelDataTypes();

                    $dataTypes = array_map(function($dataType) use ($reversedConstants) {
                        return $reversedConstants[$dataType];
                    }, $dataTypes);

                    $resource->setDataTypes($dataTypes);
                }
            }
        }

        $resource->importManyEndpoints($details->getEndpoints());

        $this->addResource($resource);
    }

    protected function addResource(\PhalconRest\Export\Documentation\Resource $resource)
    {
        $this->resources[] = $resource;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function getResources()
    {
        return $this->resources;
    }
}