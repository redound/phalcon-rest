<?php

namespace PhalconRest\Middleware;

use Phalcon\Events\Event;
use PhalconRest\Constants\ErrorCodes;
use PhalconRest\Exception;

class NotFoundMiddleware extends \PhalconRest\Mvc\Plugin
{
    public function beforeNotFound(Event $event, \PhalconRest\Api $api)
    {
        throw new Exception(ErrorCodes::GENERAL_NOT_FOUND);
    }
}
