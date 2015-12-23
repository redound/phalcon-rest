<?php

namespace PhalconRest\Middleware;

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

        var_dump($resource);
        var_dump($endpoint);
        exit;

        $roles = [AclRoles::NONE];

        if ($this->authManager->loggedIn()) {
            $roles = [AclRoles::NONE, AclRoles::USER];
        }

//        $allowed = $this->aclService->isAllowed($api->getCurrentResource(), $api->getCurrentEndpoint(), $roles);

        $allowed = true;

        if (!$allowed) {
            throw new Exception(ErrorCodes::ACCESS_DENIED);
        }
    }
}