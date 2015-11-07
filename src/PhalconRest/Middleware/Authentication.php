<?php

namespace PhalconRest\Middleware;

class Authentication extends \PhalconRest\Mvc\Plugin
{
    public function beforeExecuteRoute()
    {
        $token = $this->request->getToken();

        if ($token) {
            $this->authManager->authenticateToken($token);
        }
    }
}