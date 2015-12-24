<?php

namespace PhalconRest\Export\Postman;

class Collection
{
    public $id;
    public $name;
    public $basePath;
    protected $requests = [];

    public function __construct($name, $basePath)
    {
        $this->id = uniqid();
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

        $name = $route->getName() ?: $route->getPattern();

        $this->addRequest(new Request(
            $this->id,
            uniqid(),
            $name,
            null,
            $this->basePath . $route->getPattern(),
            $route->getHttpMethods(),
            'Authorization: Bearer {{authToken}}',
            null,
            "raw"
        ));
    }

    public function addRequest(Request $request)
    {
        $this->requests[] = $request;
    }

    public function importManyResources(array $resources)
    {
        /** @var \PhalconRest\Api\Resource $resource */
        foreach ($resources as $resource) {
            $this->importResource($resource);
        }
    }

    public function importResource(\PhalconRest\Api\Resource $resource)
    {
        foreach ($resource->getEndpoints() as $endpoint) {

            $this->addRequest(new Request(
                $this->id,
                uniqid(),
                $resource->getPrefix() . $endpoint->getPath(),
                $endpoint->getDescription(),
                $this->basePath . $resource->getPrefix() . $endpoint->getPath(),
                $endpoint->getHttpMethod(),
                'Authorization: Bearer {{authToken}}',
                null,
                "raw"
            ));
        }
    }

    public function getRequests()
    {
        return $this->requests;
    }
}