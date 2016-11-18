<?php

namespace PhalconRest\Export;

use PhalconRest\Api\ApiCollection;
use PhalconRest\Api\ApiResource;
use PhalconRest\Export\Documentation\ApiCollection as DocumentationCollection;
use PhalconRest\Export\Documentation\ApiEndpoint as DocumentationEndpoint;
use PhalconRest\Mvc\Plugin;
use PhalconRest\Transformers\ModelTransformer;

class Documentation extends Plugin
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
        /** @var ApiCollection $collection */
        foreach ($collections as $collection) {
            $this->addCollection($collection);
        }
    }

    public function addCollection(ApiCollection $apiCollection)
    {
        $aclRoles = $this->acl->getRoles();

        $collection = new DocumentationCollection();
        $collection->setName($apiCollection->getName());
        $collection->setDescription($apiCollection->getDescription());
        $collection->setPath($apiCollection->getPrefix());

        // Set fields
        if ($apiCollection instanceof ApiResource) {

            if ($modelClass = $apiCollection->getModel()) {

                if ($transformerClass = $apiCollection->getTransformer()) {

                    /** @var ModelTransformer $transformer */
                    $transformer = new $transformerClass;

                    if ($transformer instanceof ModelTransformer) {

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
        foreach ($apiCollection->getEndpoints() as $apiEndpoint) {
            $endpoint = new DocumentationEndpoint();
            $endpoint->setName($apiEndpoint->getName());
            $endpoint->setDescription($apiEndpoint->getDescription());
            $endpoint->setHttpMethod($apiEndpoint->getHttpMethod());
            $endpoint->setPath($apiEndpoint->getPath());
            $endpoint->setExampleResponse($apiEndpoint->getExampleResponse());

            $allowedRoleNames = [];

            /** @var \Phalcon\Acl\Role $role */
            foreach ($aclRoles as $role) {

                if ($this->acl->isAllowed($role->getName(), $apiCollection->getIdentifier(),
                    $apiEndpoint->getIdentifier())
                ) {
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
