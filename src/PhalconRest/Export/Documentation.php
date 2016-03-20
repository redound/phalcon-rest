<?php

namespace PhalconRest\Export;

use Phalcon\Acl;
use PhalconRest\Transformers\ModelTransformer;

class Documentation extends \PhalconRest\Mvc\Plugin
{
    public $name;
    public $basePath;
    protected $routes = [];
    protected $collections = [];

    public function __construct($name, $basePath)
    {
        $this->name = $name;
        $this->basePath = $basePath;
    }

    public function addManyRoutes(array $routes)
    {
        /** @var \Phalcon\Mvc\Router\Route $route */
        foreach ($routes as $route) {
            $this->addRoute($route);
        }
    }

    public function addRoute(\Phalcon\Mvc\Router\Route $route)
    {
        if (@unserialize($route->getName())) {
            return;
        }

        $this->routes[] = $route;
    }

    public function addManyCollections(array $collections)
    {
        /** @var \PhalconRest\Api\Collection $collection */
        foreach ($collections as $collection) {
            $this->addCollection($collection);
        }
    }

    public function addCollection(\PhalconRest\Api\Collection $apiCollection)
    {
        $aclRoles = $this->acl->getRoles();

        $collection = new \PhalconRest\Export\Documentation\Collection();
        $collection->setName($apiCollection->getName());
        $collection->setDescription($apiCollection->getDescription());
        $collection->setPath($apiCollection->getPrefix());

        // Set fields
        if($apiCollection instanceof \PhalconRest\Api\Resource) {

            if ($modelClass = $apiCollection->getModel()) {

                if ($transformerClass = $apiCollection->getTransformer()) {

                    /** @var \PhalconRest\Transformers\ModelTransformer $transformer */
                    $transformer = new $transformerClass;

                    if ($transformer instanceof \PhalconRest\Transformers\ModelTransformer) {

                        $transformer->setModelClass($modelClass);

                        $responseFields = $transformer->getResponseProperties();
                        $dataTypes = $transformer->getModelDataTypes();

                        $fields = [];

                        foreach ($responseFields as $field) {
                            $fields[$field] = array_key_exists($field,
                                $dataTypes) ? $dataTypes[$field] : ModelTransformer::TYPE_UNKNOWN;
                        }

                        $collection->setFields($fields);
                    }
                }
            }
        }

        // Add endpoints
        foreach($apiCollection->getEndpoints() as $apiEndpoint)
        {
            $endpoint = new \PhalconRest\Export\Documentation\Endpoint();
            $endpoint->setName($apiEndpoint->getName());
            $endpoint->setDescription($apiEndpoint->getDescription());
            $endpoint->setHttpMethod($apiEndpoint->getHttpMethod());
            $endpoint->setPath($apiEndpoint->getPath());
            $endpoint->setExampleResponse($apiEndpoint->getExampleResponse());

            $allowedRoleNames = [];

            /** @var \Phalcon\Acl\Role $role */
            foreach($aclRoles as $role){

                if($this->acl->isAllowed($role->getName(), $apiCollection->getIdentifier(), $apiEndpoint->getIdentifier())){
                    $allowedRoleNames[] = $role->getName();
                }
            }

            $endpoint->setAllowedRoles($allowedRoleNames);

            $collection->addEndpoint($endpoint);
        }

        $this->collections[] = $collection;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function getCollections()
    {
        return $this->collections;
    }
}