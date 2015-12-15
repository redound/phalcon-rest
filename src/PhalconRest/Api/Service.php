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

    public function addResource(Resource $resource)
    {
        $this->resources[$resource->getKey()] = $resource;
    }

    public function getResource($name)
    {
        if (!array_key_exists($name, $this->resources)) {
            throw new UserException(ErrorCodes::DATA_NOTFOUND, 'Resource has not been found');
        }

        return $this->resources[$name];
    }
}