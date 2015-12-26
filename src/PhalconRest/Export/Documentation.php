<?php

namespace PhalconRest\Export;

use Phalcon\Acl;
use PhalconRest\Transformers\ModelTransformer;

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

    public function addManyResources(array $resources)
    {
        /** @var \PhalconRest\Api\Resource $resource */
        foreach ($resources as $resource) {
            $this->addResource($resource);
        }
    }

    public function addResource(\PhalconRest\Api\Resource $apiResource)
    {
        $aclRoles = $this->acl->getRoles();

        $resource = new \PhalconRest\Export\Documentation\Resource();
        $resource->setName($apiResource->getName());
        $resource->setDescription($apiResource->getDescription());
        $resource->setPath($apiResource->getPrefix());

        // Set fields
        if ($modelClass = $apiResource->getModel()) {

            if ($transformerClass = $apiResource->getTransformer()) {

                /** @var \PhalconRest\Transformers\ModelTransformer $transformer */
                $transformer = new $transformerClass;

                if ($transformer instanceof \PhalconRest\Transformers\ModelTransformer) {

                    $transformer->setModelClass($modelClass);

                    $responseFields = $transformer->getResponseProperties();
                    $dataTypes = $transformer->getModelDataTypes();

                    $fields = [];

                    foreach($responseFields as $field){
                        $fields[$field] = array_key_exists($field, $dataTypes) ? $dataTypes[$field] : ModelTransformer::TYPE_UNKNOWN;
                    }

                    $resource->setFields($fields);
                }
            }
        }

        // Add endpoints
        foreach($apiResource->getEndpoints() as $apiEndpoint)
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

                if($this->acl->isAllowed($role->getName(), $apiResource->getIdentifier(), $apiEndpoint->getIdentifier())){
                    $allowedRoleNames[] = $role->getName();
                }
            }

            $endpoint->setAllowedRoles($allowedRoleNames);

            $resource->addEndpoint($endpoint);
        }

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