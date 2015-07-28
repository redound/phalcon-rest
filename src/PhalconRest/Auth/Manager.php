<?php

namespace PhalconRest\Auth;

use PhalconRest\Constants\ErrorCodes as ErrorCodes;
use PhalconRest\Exceptions\UserException;

class Manager extends \Phalcon\Mvc\User\Plugin
{

    protected $user;
    protected $issuer;
    protected $expireTime;
    protected $accounts;
    protected $token;
    protected $genSalt;

    public function __construct(\PhalconRest\Auth\Session $sessionManager)
    {
        $this->issuer = null;
        $this->expireTime = 86400 * 7; // Default one week
        $this->accounts = [];
        $this->sessionManager = $sessionManager;

        return $this;
    }

    public function setGenSalt($salt)
    {
        $this->genSalt = $salt;
    }

    public function addAccount($name, \PhalconRest\Auth\Account $account)
    {
        $this->accounts[$name] = $account;

        return $this;
    }

    public function getAccounts()
    {
        return $this->accounts;
    }

    public function setExpireTime($time)
    {
        $this->expireTime = $time;

        return $this;
    }

    public function getExpireTime()
    {
        return $this->expireTime;
    }

    public function setIssuer($issuer)
    {
        $this->issuer = $issuer;

        return $issuer;
    }

    public function getIssuer()
    {
        return $this->issuer;
    }

    public function setSessionManager($session)
    {
        $this->sessionManager = $session;

        return $this;
    }

    public function getSessionManager()
    {
        return $this->sessionManager;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function loggedIn()
    {
        return !!$this->user;
    }

    public function getAccount($name)
    {
        if (array_key_exists($name, $this->accounts)) {

            return $this->accounts[$name];
        }

        return false;
    }

    public function register($bearer, $data)
    {
        
        // Check if account type exists
        if (!$account = $this->getAccount($bearer)) {

            throw new UserException(ErrorCodes::DATA_NOTFOUND);
        }

        // Check if account has method
        if (!method_exists($account, 'register')) {

            throw new UserException(ErrorCodes::DATA_NOTFOUND);
        }

        $user = $account->register($data);

        return $user;
    }
    
    public function update($bearer, $data)
    {

        // Check if account type exists
        if (!$account = $this->getAccount($bearer)) {

            throw new UserException(ErrorCodes::DATA_NOTFOUND);
        }

        // Check if account has method
        if (!method_exists($account, 'update')) {

            throw new UserException(ErrorCodes::DATA_NOTFOUND);
        }

        $user = $account->update($data);

        return $user;
    }

    public function changepassword($bearer, $data)
    {

        // Check if account type exists
        if (!$account = $this->getAccount($bearer)) {

            throw new UserException(ErrorCodes::DATA_NOTFOUND);
        }

        // Check if account has method
        if (!method_exists($account, 'changepassword')) {

            throw new UserException(ErrorCodes::DATA_NOTFOUND);
        }

        $user = $account->changepassword($data);

        return $user;
    }

    public function login($bearer, $username, $password)
    {
        $this->setIssuer($bearer);

        if (!$account = $this->getAccount($bearer)) {

            throw new UserException(ErrorCodes::AUTH_INVALIDTYPE);
        }

        $user = $account->login($username, $password);

        if (!$user) {

            throw new UserException(ErrorCodes::AUTH_BADLOGIN);
        }

        $this->setUser($user);

        return $this;
    }

    public function getToken($key = null)
    {
        if (!$this->token) {

            $this->token = $this->sessionManager->create($this->getIssuer(), $this->getUser(), time(), $this->getExpireTime());
        }

        if ($key) {

            return $this->token[$key];
        }

        return $this->token;
    }

    public function hasToken()
    {
        return !!$this->token;
    }

    public function authenticateToken($token)
    {
        try {

            $decoded = $this->sessionManager->decode($token);
        } catch (\UnexpectedValueException $e) {

            return false;
        }

        // Set session
        if ($decoded && $decoded->exp > time()) {

            $this->setUser($decoded->sub);
        }

        return true;
    }

    public function getTokenResponse()
    {
        return [
            'AuthToken' => $this->sessionManager->encode($this->getToken()),
            'Expires' => $this->getToken('exp'),
            'AccountType' => $this->getIssuer()
        ];
    }

    public function createMailToken()
    {
        return password_hash($this->genSalt . rand(0, 10), PASSWORD_DEFAULT);
    }
}
