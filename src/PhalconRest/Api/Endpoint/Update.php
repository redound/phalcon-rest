<?php

namespace PhalconRest\Api\Endpoint;

use PhalconRest\Api\Endpoint;
use PhalconRest\Constants\Http;

class Update extends Endpoint
{
    public function __construct($path='/{id}', $httpMethod=Http::PUT, $handlerMethod='update')
    {
        parent::__construct($path, $httpMethod, $handlerMethod);
    }
}