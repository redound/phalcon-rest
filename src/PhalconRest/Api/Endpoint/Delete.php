<?php

namespace PhalconRest\Api\Endpoint;

use PhalconRest\Api\Endpoint;
use PhalconRest\Constants\Http;

class Delete extends Endpoint
{
    public function __construct($path='/{id}', $httpMethod=Http::DELETE, $handlerMethod='delete')
    {
        parent::__construct($path, $httpMethod, $handlerMethod);
    }
}