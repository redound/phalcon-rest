<?php

namespace PhalconRest\Auth\Account;

use PhalconRest\Constants\ErrorCodes as ErrorCodes;
use PhalconRest\Exceptions\UserException;

class Google extends \Phalcon\Mvc\User\Plugin implements \PhalconRest\Auth\Account
{
    protected $name;
    protected $userModel;
    protected $googleClient;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function setGoogleClient(\PhalconRest\Facades\GoogleClient $googleClient = null)
    {
        $this->googleClient = $googleClient;
    }

    public function setUserModel(\Phalcon\Mvc\Model $userModel)
    {
        $this->userModel = get_class($userModel);
    }

    public function login($username = null, $password = null)
    {
        $userModel = $this->userModel;

        // Authenticate
        if (!$this->googleClient->authenticate($username)) {

            throw new UserException(ErrorCodes::GOOGLE_BADLOGIN, 'Login invalid. Could not verify your account with Google');
        }

        // Payload contains user data from Google
        $payload = $this->googleClient->getPayload();

        if (!$payload) {

            throw new UserException(ErrorCodes::GOOGLE_NODATA, 'Error getting payload');
        }

        return $userModel::processGooglePayload($payload);
    }
}
