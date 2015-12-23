<?php

namespace PhalconRest\Middleware;

use PhalconRest\Constants\ErrorCodes;
use PhalconRest\Exception;
use Phalcon\Events\Event;

class NotFoundMiddleware extends \PhalconRest\Mvc\Plugin
{
    public function beforeNotFound(Event $event, \PhalconRest\Api $api)
    {
        throw new Exception(ErrorCodes::GENERAL_NOT_FOUND);
    }
}
