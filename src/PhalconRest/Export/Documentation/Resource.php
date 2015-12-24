<?php

namespace PhalconRest\Export\Documentation;

class Resource
{
    protected $resource;
    protected $endpoints = [];
    protected $allowedRolesPerEndpoint = [];
    protected $deniedRolesPerEndpoint = [];
    protected $source;
    protected $columnMap;
    protected $whitelist;
    protected $dataTypes;

    public function __construct($allowedRolesPerEndpoint = [], $deniedRolesPerEndpoint = [])
    {
        $this->allowedRolesPerEndpoint = $allowedRolesPerEndpoint;
        $this->deniedRolesPerEndpoint = $deniedRolesPerEndpoint;
    }

    public function setDetails(\PhalconRest\Api\Resource $details)
    {
        $this->details = $details;
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function setSource($source)
    {
        $this->source = $source;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function setColumnMap($columnMap)
    {
        $this->columnMap = $columnMap;
    }

    public function getColumnMap()
    {
        return $this->columnMap;
    }

    public function setWhitelist($whitelist)
    {
        $this->whitelist = $whitelist;
    }

    public function getWhitelist()
    {
        return $this->whitelist;
    }

    public function setDataTypes($dataTypes)
    {
        $this->dataTypes = $dataTypes;
    }

    public function getDataTypes()
    {
        return $this->dataTypes;
    }

    public function importManyEndpoints(array $endpoints)
    {
        foreach($endpoints as $endpoint) {
            $this->importEndpoint($endpoint);
        }
    }

    public function importEndpoint(\PhalconRest\Api\Endpoint $details)
    {
        $endpoint = new Endpoint();
        $endpoint->setDetails($details);

        foreach($this->allowedRolesPerEndpoint as $roleConfig) {

            /** @var \Phalcon\Acl\Role $role */
            $role = $roleConfig[0];

            if ($roleConfig[2] == $details->getIdentifier()) {
                $endpoint->addAllowedRole($role->getName());
            }
        }

        foreach($this->deniedRolesPerEndpoint as $roleConfig) {

            /** @var \Phalcon\Acl\Role $role */
            $role = $roleConfig[0];

            if ($roleConfig[2] == $details->getIdentifier()) {
                $endpoint->addDeniedRole($role->getName());
            }
        }

        $this->addEndpoint($endpoint);
    }

    protected function addEndpoint($endpoint)
    {
        $this->endpoints[] = $endpoint;
    }

    public function getEndpoints()
    {
        return $this->endpoints;
    }
}