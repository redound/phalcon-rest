<?php

namespace PhalconRest\Auth;

interface AccountType
{
    /**
     * @param array $data Login data
     *
     * @return string Identity
     */
    public function login($data);

    /**
     * @param string $identity Identity
     *
     * @return bool Authentication successful
     */
    public function authenticate($identity);
}
