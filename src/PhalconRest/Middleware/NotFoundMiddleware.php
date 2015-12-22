<?php

namespace PhalconRest\Middleware;

use PhalconRest\Constant\ErrorCode;
use PhalconRest\Exception;
use Phalcon\Events\Event;

class NotFoundMiddleware extends \PhalconRest\Mvc\Plugin
{
    public function beforeNotFound(Event $event, \PhalconRest\Api $api)
    {
        throw new Exception(ErrorCode::GEN_NOTFOUND);
    }
}
