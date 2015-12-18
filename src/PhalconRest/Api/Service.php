<?php

namespace PhalconRest\Api;

use PhalconRest\Constants\ErrorCodes;
use PhalconRest\Exceptions\UserException;

class Service extends \PhalconRest\Mvc\Plugin
{
    public $resources;

    public function __construct()
    {
        $this->resources = [];
    }

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

    public function setResource($name, Resource $resource)
    {
        $this->resources[$name] = $resource;
        return $this;
    }

    public function removeResource($name)
    {
        unset($this->resources[$name]);
    }

    public function setResources($resources)
    {
        $this->resources = $resources;
        return $this;
    }
}