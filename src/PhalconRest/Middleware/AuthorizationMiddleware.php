<?php

namespace PhalconRest\Middleware;

use App\Constants\Services;
use Phalcon\Acl;
use Phalcon\Events\Event;
use PhalconRest\Constants\AclRoles;
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

        $allowed = $acl->isAllowed(AclRoles::ADMINISTRATOR, $resource->getPrefix(), $endpoint->getIdentifier());

        if (!$allowed) {
            throw new Exception(ErrorCodes::ACCESS_DENIED);
        }
    }
}