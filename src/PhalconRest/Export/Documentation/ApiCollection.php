<?php

namespace PhalconRest\Export\Documentation;

class ApiCollection
{
    protected $name;
    protected $description;
    protected $path;

    protected $endpoints = [];
    protected $fields;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    public function addManyEndpoints(array $endpoints)
    {
        foreach ($endpoints as $endpoint) {
            $this->addEndpoint($endpoint);
        }
    }

    public function addEndpoint(ApiEndpoint $endpoint)
    {
        $this->endpoints[] = $endpoint;
    }

    public function getEndpoints()
    {
        return $this->endpoints;
    }
}
