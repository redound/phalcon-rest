<?php

namespace PhalconRest\Middleware;

use App\Constants\Services;
use Phalcon\Acl;
use Phalcon\Events\Event;
use PhalconRest\Constants\ErrorCodes;
use PhalconRest\Exception;

class AuthorizationMiddleware extends \PhalconRest\Mvc\Plugin
{
    public function beforeExecuteRoute(Event $event, \PhalconRest\Api $api)
    {
        $resource = $api->getMatchedResource();
        $endpoint = $api->getMatchedEndpoint();

        if (!$resource || !$endpoint) {
            return;
        }

        /** @var \Phalcon\Acl\Adapter $acl */
        $acl = $this->di->get(Services::ACL);

        // TODO: Get the right role
        $allowed = $acl->isAllowed('administrator', $resource->getIdentifier(), $endpoint->getIdentifier());

        if (!$allowed) {
            throw new Exception(ErrorCodes::ACCESS_DENIED);
        }
    }
}