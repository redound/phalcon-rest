<?php

namespace PhalconRest\Documentation;

class Generator extends \Phalcon\Mvc\User\Plugin
{
    protected $reader;
    protected $collections;
    protected $collectionClasses = [];
    protected $handlers = [];

    public function __construct($collections = [])
    {
        $this->collections = $collections;
        $this->reader = new \Phalcon\Annotations\Adapter\Memory();
    }

    protected function getCollectionClasses()
    {
        if (empty($this->collectionClasses)) {

            foreach ($this->collections as $collection) {

                $this->collectionClasses[] = new $collection;
            }
        }

        return $this->collectionClasses;
    }

    protected function getAnnotationsFromCollection($collection)
    {
        $handler = $collection->getHandler();
        $reflector = $this->reader->get($handler);
        return [
            "class" => $reflector->getClassAnnotations(),
            "methods" => $reflector->getMethodsAnnotations(),
        ];
    }

    protected function getAnnotationsFromCollections()
    {
        $data = [];

        foreach ($this->getCollectionClasses() as $collection) {

            $handler = $collection->getHandler();
            $annotations = $this->getAnnotationsFromCollection($collection);
            $classAnnotations = $annotations["class"];

            $resource = "Untitled Resource - Specify @resource above class";

            if (is_object($classAnnotations) && $classAnnotations->has("resource")) {

                $resource = $classAnnotations->get("resource")->getArgument(0);
            }

            $data[$handler]["resource"] = $resource;
            $data[$handler]["collection"] = $collection;
            $data[$handler]["annotations"] = $annotations["methods"];
        }

        return $data;
    }

    protected function getDocumentedRoutesFromCollections()
    {
        $collections = $this->getAnnotationsFromCollections();
        $data = [];

        foreach ($collections as $collection) {

            $data[] = new Resource($collection["resource"], $collection["collection"], $collection["annotations"]);
        }

        return $data;
    }

    public function generate()
    {
        return $this->getDocumentedRoutesFromCollections();
    }

    public function generatePostmanCollection($hostName = 'http://no-hostname-defined.local/')
    {
        $resources = $this->getDocumentedRoutesFromCollections();

        $requests = [];
        $collectionId = uniqid();

        foreach ($resources as $resource) {

            foreach ($resource->endpoints as $endpoint) {
                $data = null;

                if (is_object($endpoint->requestExample) || is_array($endpoint->requestExample)) {

                    $data = $endpoint->requestExample;
                }

                $request = new \stdClass;
                $request->collectionId = $collectionId;
                $request->id = uniqid();
                $request->name = $endpoint->resource . ' - ' . $endpoint->title;
                $request->description = $endpoint->description;
                $request->url = $hostName . $endpoint->route;
                $request->method = $endpoint->method;
                $request->headers = "Authorization: Bearer {{authToken}}";
                $request->data = $data ? json_encode($data) : null;
                $request->dataMode = "raw";
                $requests[] = $request;
            }
        }

        $collection = new \stdClass;
        $collection->id = $collectionId;
        $collection->name = $hostName;
        $collection->requests = $requests;

        return $collection;
    }
}
