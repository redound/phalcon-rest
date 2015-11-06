<?php

namespace PhalconRest\Documentation;

class Resource
{
    protected $methods;
    protected $collection;
    public $endpoints = [];

    public function __construct($resource, $collection, $annotations)
    {

        $this->resource = $resource;
        $this->collection = $collection;
        $this->parseHandlers($collection->getHandlers());
        $this->parseAnnotations($annotations);
    }

    protected function getMethod($method)
    {

        return isset($this->methods[$method]) ? $this->methods[$method] : false;
    }

    protected function parseHandlers($handlers)
    {

        foreach ($handlers as $handler) {

            $method = $handler[2];
            $this->methods[$method] = $handler;
        }
    }

    protected function parseAnnotations($methods)
    {

        if (empty($methods)) {
            return;
        }

        foreach ($methods as $method => $annotations) {

            $docEndpoint = new Endpoint($this->resource);

            if ($this->getMethod($method)) {

                $methodData = $this->getMethod($method);
                $docEndpoint->method = $methodData[0];
                $docEndpoint->route = $this->collection->getPrefix() . $methodData[1];
            }

            foreach ($annotations as $description) {

                $field = $description->getName();
                $value = $description->getArgument(0);

                $docEndpoint->$field = $value;
            }

            $this->endpoints[] = $docEndpoint;
        }
    }
}
