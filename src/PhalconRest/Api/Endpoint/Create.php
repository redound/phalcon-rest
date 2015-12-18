<?php

namespace PhalconRest\Api\Endpoint;

use PhalconRest\Api\Endpoint;
use PhalconRest\Constants\Http;

class Create extends Endpoint
{
    public function __construct($path='/{id}', $httpMethod=Http::POST, $handlerMethod='create')
    {
        parent::__construct($path, $httpMethod, $handlerMethod);
    }
}