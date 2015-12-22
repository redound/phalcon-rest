<?php

namespace PhalconRest\Middleware;

use Phalcon\Events\Event;
use PhalconRest\Constant\AclRole;
use PhalconRest\Constant\ErrorCode;
use PhalconRest\Exception;

class AuthorizationMiddleware extends \PhalconRest\Mvc\Plugin
{
    public function beforeExecuteRoute(Event $event, \PhalconRest\Api $api)
    {
        $roles = [AclRole::NONE];

        if ($this->authManager->loggedIn()) {
            $roles = [AclRole::NONE, AclRole::USER];
        }

//        $allowed = $this->aclService->isAllowed($api->getCurrentResource(), $api->getCurrentEndpoint(), $roles);

        $allowed = true;

        if (!$allowed) {
            throw new Exception(ErrorCode::ACL_FORBIDDEN);
        }
    }
}