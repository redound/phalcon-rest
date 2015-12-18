<?php

namespace PhalconRest\Api\Endpoint;

use PhalconRest\Api\Endpoint;
use PhalconRest\Constants\Http;

class Find extends Endpoint
{
    public function __construct($path='/{id}', $httpMethod=Http::GET, $handlerMethod='find')
    {
        parent::__construct($path, $httpMethod, $handlerMethod);
    }
}