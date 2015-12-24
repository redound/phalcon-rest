<?php

namespace PhalconRest\Export\Documentation;

class Endpoint
{
    protected $details;
    protected $allowedRoles = [];
    protected $deniedRoles = [];

    public function setDetails(\PhalconRest\Api\Endpoint $details)
    {
        $this->details = $details;
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function addAllowedRole($role)
    {
        $this->allowedRoles[] = $role;
    }

    public function addDeniedRole($role)
    {
        $this->deniedRoles[] = $role;
    }

    public function getAllowedRoles()
    {
        return $this->allowedRoles;
    }

    public function getDeniedRoles()
    {
        return $this->deniedRoles;
    }
}