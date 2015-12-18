<?php

namespace PhalconRest\Middleware;

use Phalcon\Events\Event;

class Authentication extends \PhalconRest\Mvc\Plugin
{
    public function beforeExecuteRoute(Event $event, \PhalconRest\Api $api)
    {
        $token = $this->request->getToken();

        if ($token) {
            $this->authManager->authenticateToken($token);
        }
    }
}