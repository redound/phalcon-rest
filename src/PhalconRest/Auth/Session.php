<?php

namespace PhalconRest\Auth;

class Session
{
    /**
     * @var mixed Identity of the session
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

    protected $expirationTime;

    protected $isValid;


    public function __construct($accountTypeName, $identity, $expirationTime, $token=null)
    {
        $this->accountTypeName = $accountTypeName;
        $this->identity = $identity;
        $this->expirationTime = $expirationTime;
        $this->token = $token;
    }

    public function setIdentity($identity)
    {
        $this->identity = $identity;

        return $this;
    }

    public function getIdentity()
    {
        return $this->identity;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setExpirationTime($time)
    {
        $this->expirationTime = $time;
    }

    public function getExpirationTime()
    {
        return $this->expirationTime;
    }

    public function setAccountTypeName($accountTypeName)
    {
        $this->accountTypeName = $accountTypeName;
    }

    public function getAccountTypeName()
    {
        return $this->accountTypeName;
    }
}