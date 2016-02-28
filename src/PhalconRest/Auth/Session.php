<?php

namespace PhalconRest\Auth;

class Session
{
    /**
     * @var string Identity of the session
     */
    protected $identity;

    /**
     * @var string Account-type name of the session
     */
    protected $accountTypeName;

    /**
     * @var string Session token
     */
    protected $token;

    protected $startTime;
    protected $expirationTime;


    public function __construct($accountTypeName, $identity, $startTime, $expirationTime, $token = null)
    {
        $this->accountTypeName = $accountTypeName;
        $this->identity = $identity;
        $this->startTime = $startTime;
        $this->expirationTime = $expirationTime;
        $this->token = $token;
    }

    public function getIdentity()
    {
        return $this->identity;
    }

    public function setIdentity($identity)
    {
        $this->identity = $identity;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getExpirationTime()
    {
        return $this->expirationTime;
    }

    public function setExpirationTime($time)
    {
        $this->expirationTime = $time;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function setStartTime($time)
    {
        $this->startTime = $time;
    }

    public function getAccountTypeName()
    {
        return $this->accountTypeName;
    }

    public function setAccountTypeName($accountTypeName)
    {
        $this->accountTypeName = $accountTypeName;
    }
}