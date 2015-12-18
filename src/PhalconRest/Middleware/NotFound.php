<?php

namespace PhalconRest\Middleware;

use PhalconRest\Constants\ErrorCodes;
use PhalconRest\Exceptions\UserException;
use Phalcon\Events\Event;

class NotFound extends \PhalconRest\Mvc\Plugin
{
    public function beforeNotFound(Event $event, \PhalconRest\Api $api)
    {
        throw new UserException(ErrorCodes::GEN_NOTFOUND);
    }
}
