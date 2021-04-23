<?php

namespace PhalconRest\Middleware;

use Phalcon\Acl;
use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use Phalcon\Di\Injectable;
use PhalconRest\Api;
use PhalconRest\Constants\ErrorCodes;
use PhalconRest\Exception;

class AuthorizationMiddleware extends Injectable implements MiddlewareInterface
{
    public function beforeExecuteRoute(Event $event, Api $api)
    {
        $collection = $api->getMatchedCollection();
        $endpoint = $api->getMatchedEndpoint();

        if (!$collection || !$endpoint) {
            return;
        }
        
        $allowed = $this->acl->isAllowed($this->userService->getRole(), $collection->getIdentifier(),
            $endpoint->getIdentifier());

        if (!$allowed) {
            throw new Exception(ErrorCodes::ACCESS_DENIED);
        }
    }

    public function call(Micro $api)
    {
        return true;
    }
}