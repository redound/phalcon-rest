<?php

namespace PhalconRest\User;

use PhalconRest\Constants\ErrorCodes;
use PhalconRest\Exception;

class Service extends \PhalconRest\Mvc\Plugin
{
    /**
     * Returns details for the current user, e.g. a User model
     *
     * @return mixed
     * @throws Exception
     */
    public function getDetails()
    {
        $details = null;

        $session = $this->authManager->getSession();
        if ($session) {

            $identity = $session->getIdentity();
            $details = $this->getDetailsForIdentity($identity);
        }

        return $details;
    }

    /**
     * This method should return the role for the current user
     *
     * @return string Name of the role for the current user
     * @throws Exception
     */
    public function getRole()
    {
        throw new Exception(ErrorCodes::GENERAL_NOT_IMPLEMENTED,
            'Unable to get role for identity, method getRole in user service not implemented. ' .
            'Make a subclass of \PhalconRest\User\Service with an implementation for this method, and register it in your DI.');
    }

    /**
     * This method should return details for the provided identity. Override this method with your own implementation.
     *
     * @param mixed $identity Identity to get the details from
     *
     * @return mixed The details for the provided identity
     * @throws Exception
     */
    protected function getDetailsForIdentity($identity)
    {
        throw new Exception(ErrorCodes::GENERAL_NOT_IMPLEMENTED,
            'Unable to get details for identity, method getDetailsForIdentity in user service not implemented. ' .
            'Make a subclass of \PhalconRest\User\Service with an implementation for this method, and register it in your DI.');
    }
}
