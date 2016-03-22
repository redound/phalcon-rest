<?php

namespace PhalconRest\Middleware;

use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use PhalconRest\Api;
use PhalconRest\Constants\ErrorCodes;
use PhalconRest\Exception;
use PhalconRest\Mvc\Plugin;

class NotFoundMiddleware extends Plugin implements MiddlewareInterface
{
    public function beforeNotFound()
    {
        throw new Exception(ErrorCodes::GENERAL_NOT_FOUND);
    }

    public function call(Micro $api) {

        return true;
    }
}
