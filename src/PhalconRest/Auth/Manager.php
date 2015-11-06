<?php

namespace PhalconRest\Auth;

use PhalconRest\Constants\ErrorCodes as ErrorCodes;
use PhalconRest\Exceptions\UserException;

class Manager extends \PhalconRest\Mvc\Plugin
{
    const LOGIN_DATA_USERNAME = "username";
    const LOGIN_DATA_PASSWORD = "password";

    /**
     * @var AccountType[] Account types
     */
    protected $accountTypes;

    /**
     * @var Session Currenty active session
     */
    protected $session;

    protected $sessionExpirationTime;


    public function __construct($sessionExpirationTime = 24 * 3600)
    {
        $this->sessionExpirationTime = $sessionExpirationTime;

        $this->accountTypes = [];
        $this->session = null;
    }


    public function registerAccountType($name, AccountType $account)
    {
        $this->accountTypes[$name] = $account;

        return $this;
    }

    public function getAccountTypes()
    {
        return $this->accountTypes;
    }


    public function getSessionExpirationTime()
    {
        return $this->sessionExpirationTime;
    }

    public function setSessionExpirationTime($time)
    {
        $this->sessionExpirationTime = $time;
    }


    public function getSession()
    {
        return $this->session;
    }

    public function setSession(Session $session)
    {
        $this->session = $session;
    }


    /**
     * @return bool
     *
     * Check if a user is currently logged in
     */
    public function loggedIn()
    {
        return !!$this->session;
    }

    /**
     * @param $name
     *
     * @return \PhalconRest\Auth\AccountType Account-type
     */
    public function getAccountType($name)
    {
        if (array_key_exists($name, $this->accountTypes)) {

            return $this->accountTypes[$name];
        }

        return false;
    }


    /**
     * @param string $accountTypeName
     * @param array $data
     *
     * @return Session Created session
     * @throws UserException
     *
     * Login a user with the specified account-type
     */
    public function login($accountTypeName, array $data)
    {
        if (!$account = $this->getAccountType($accountTypeName)) {

            throw new UserException(ErrorCodes::AUTH_INVALIDTYPE);
        }

        $identity = $account->login($data);

        if (!$identity) {

            throw new UserException(ErrorCodes::AUTH_BADLOGIN);
        }

        $session = new Session($accountTypeName, $identity, $this->sessionExpirationTime);
        $token = $this->tokenParser->getToken($session);
        $session->setToken($token);

        $this->session = $session;

        return $this->session;
    }

    public function loginWithUsernamePassword($accountTypeName, $username, $password)
    {
        return $this->login($accountTypeName, [

            self::LOGIN_DATA_USERNAME => $username,
            self::LOGIN_DATA_PASSWORD => $password
        ]);
    }

    /**
     * @param string $token Token to authenticate with
     *
     * @return bool
     * @throws UserException
     */
    public function authenticateToken($token)
    {
        $session = $this->tokenParser->getSession($token);
        if(!$session){
            return false;
        }

        $session->setToken($token);

        // Authenticate identity
        if (!$account = $this->getAccountType($session->getAccountTypeName())) {

            throw new UserException(ErrorCodes::DATA_NOTFOUND);
        }

        if($account->authenticate($session->getIdentity())){

            $this->session = $session;
        }

        return !!$this->session;
    }
}
