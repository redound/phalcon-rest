<?php

namespace PhalconRest\Auth;

interface TokenParser
{
    /**
     * @param Session $session Session to generate token for
     *
     * @return string Generated token
     */
    public function getToken(Session $session);

    /**
     * @param string $token Access token
     *
     * @return Session Session restored from token
     */
    public function getSession($token);
}