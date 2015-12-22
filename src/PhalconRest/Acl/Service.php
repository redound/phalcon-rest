<?php

namespace PhalconRest\Acl;

use PhalconRest\Api\Endpoint;
use PhalconRest\Api\Resource;

class Service
{
    public function isAllowed(Resource $resource, Endpoint $endpoint, $roles = [])
    {
        return true;
    }
}