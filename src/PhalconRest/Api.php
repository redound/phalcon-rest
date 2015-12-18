<?php

namespace PhalconRest;

use PhalconRest\Api\Resource;

class Api extends \Phalcon\Mvc\Micro
{
    public $resources = [];

    /**
     * @return Resource[]
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * @param $name
     *
     * @return Resource
     */
    public function getResource($name)
    {
        return array_key_exists($name, $this->resources) ? $this->resources[$name] : null;
    }

    public function resource($name, Resource $resource)
    {
        $this->resources[$name] = $resource;
        $this->mount($resource);

        return $this;
    }
}