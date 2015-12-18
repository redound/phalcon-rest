<?php

namespace PhalconRest\Api\Endpoint;

use PhalconRest\Api\Endpoint;
use PhalconRest\Constants\Http;

class All extends Endpoint
{
    public function __construct($path='/', $httpMethod=Http::GET, $handlerMethod='all')
    {
        parent::__construct($path, $httpMethod, $handlerMethod);
    }
}